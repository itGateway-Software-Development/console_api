<?php

namespace App\Modules\Auth\Controllers\Api\V1;

use App\Mail\OTPMail;
use Illuminate\Http\Request;
use App\Modules\User\Models\User;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Modules\Auth\Requests\LoginRequest;
use App\Modules\Auth\Requests\RegisterRequest;

class AuthController extends Controller
{
    public function register(RegisterRequest $request) {
        DB::beginTransaction();
        logger($request->all());

        try {

            $otp = random_int(100000, 999999);
            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->email_otp = $otp;
            $user->save();

            $token = $user->createToken('itGateway')->plainTextToken;


            $response = [
                'status' => 'success',
                'user' => $user,
                'token' => $token
            ];

            Mail::to($request->email)->send(new OTPMail($otp, 'email_verify'));

            DB::commit();

            return response()->json([...$response], 201);
        } catch(\Exception $error) {
            logger($error);
            DB::rollBack();

            return response()->json(['message' => 'register error', 'error' => $error->getMessage()],400);
        }
    }

    public function login(LoginRequest $request) {
        logger($request->all());
        $user = User::where('email', $request->input('email'))->first();
        $otp = random_int(100000, 999999);

        if(!$user || !Hash::check($request->input('password'), $user->password)) {
            return response()->json(['message' => 'wrong credentials'], 401);
        }

        $token = $user->createToken('romanticunderwear')->plainTextToken;

        $response = [
            'status' => 'success',
            'user' => $user,
            'token' => $token
        ];

        if(is_null($user->email_verified_at)) {
            $user->email_otp = $otp;
            $user->update();

            Mail::to($request->email)->send(new OTPMail( $otp, 'email_verify'));

            return response()->json(['status' => 'not_verify', 'message' => 'Please Verify Your Email', 'user' => $user, 'token' => $token]);
        }

        return response()->json([...$response], 201);
    }

    public function verifyEmail(Request $request) {
       if($request->id) {
            $user = User::find($request->id);

            if($user) {
                if($user->email_otp == $request->input_otp) {
                    $user->email_verified_at = now();
                    $user->update();

                    return response()->json(['status' => 'success', 'message' => 'Email verified successfully']);
                } else {
                    return response()->json(['status' => 'error', 'message' => 'Wrong OTP. Please try again !']);
                }
            }
       } else {
            return response()->json(['status' => 'error', 'message' => 'Credential wrong !']);
       }
    }
}
