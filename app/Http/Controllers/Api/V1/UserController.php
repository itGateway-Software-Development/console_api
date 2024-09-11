<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\User;
use App\Mail\OTPMail;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\Api\UserRequest;

class UserController extends Controller
{
    public function updateProfile(UserRequest $request) {
        if($request->id && User::find($request->id)) {
            $user = User::find($request->id);
            $user->name = $request->name;
            $user->phone = $request->phone;
            $user->address = $request->address;
            $user->update();

            $response = [
                'status' => 'success',
                'message' => 'Successfully Updated',
                'user' => $user,
            ];

            return response()->json([...$response], 201);

        }

        return response()->json(['status' => 'error', 'message' => 'User not found']);
    }

    public function changePassword(Request $request) {
        if($request->id && User::find($request->id)) {
            $user = User::find($request->id);

            if(!$user || !Hash::check($request->input('old_password'), $user->password)) {
                return response()->json(['status' => 'error', 'message' => 'Current Password is wrong !']);
            }

            if($request->new_password != $request->new_password_confirmation) {
                return response()->json(['status' => 'error', 'message' => 'Password not match.']);
            }

            $user->password = Hash::make($request->new_password);
            $user->update();

            $response = [
                'status' => 'success',
                'message' => 'Successfully Changed Password',
                'user' => $user,
            ];

            return response()->json([...$response], 201);

        }

        return response()->json(['status' => 'error', 'message' => 'User not found']);
    }

    public function sendPwResetCode(Request $request) {
        try {
            if($request->email) {
                $otp = random_int(100000, 999999);

                $user = User::where('email', $request->email)->first();

                if($user) {
                    $user->reset_code = Hash::make($otp);
                    $user->update();

                    Mail::to($request->email)->send(new OTPMail($otp,'reset_pw'));
                    return response()->json(['status' => 'success', 'message' => 'Successfully Sent']);
                } else {
                    return response()->json(['status' => 'error', 'message' => 'Your email is not correct']);
                }
            }
        } catch(\Exception $error) {
            return response()->json(['status' => 'error', 'message' => $error->getMessage()]);
        }
    }

    public function checkPwResetCode(Request $request) {
        try {
            if($request->email && $request->resetCode) {
                logger($request->all());

                $user = User::where('email', $request->email)->first();

                if(!$user || !Hash::check($request->input('resetCode'), $user->reset_code)) {
                    return response()->json(data: ['status' => 'error', 'message' => 'Wrong code']);
                }

                return response()->json(['status' => 'success', 'message' => 'Success Check']);
            }
        } catch(\Exception $error) {
            return response()->json(['status' => 'error', 'message' => $error->getMessage()]);
        }
    }

    public function resetPassword(Request $request) {
        if($request->email) {
            $user = User::where('email', $request->email)->first();

            if(!$user) {
                return response()->json(['status' => 'error', 'message' => 'Incorrect Email.']);
            }

            if($request->new_password != $request->new_password_confirmation) {
                return response()->json(['status' => 'error', 'message' => 'Password not match.']);
            }

            $user->password = Hash::make($request->new_password);
            $user->update();

            $response = [
                'status' => 'success',
                'message' => 'Successfully Reset Password',
                'user' => $user,
            ];

            return response()->json([...$response], 201);

        }

        return response()->json(['status' => 'error', 'message' => 'User not found']);
    }
}
