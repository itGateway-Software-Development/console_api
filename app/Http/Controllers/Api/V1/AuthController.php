<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\Api\LoginRequest;
use App\Http\Requests\Api\RegisterRequest;

class AuthController extends Controller
{
    public function register(RegisterRequest $request) {
        DB::beginTransaction();

        try {
            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->password = Hash::make($request->password);
            $user->birthday = $request->birthday;
            $user->address = $request->address;
            $user->save();

            $token = $user->createToken('romanticunderwear')->plainTextToken;


            $response = [
                'message' => 'success',
                'user' => $user,
                'token' => $token
            ];

            DB::commit();

            return response()->json([...$response], 201);
        } catch(\Exception $error) {
            logger($error);
            DB::rollBack();

            return response()->json(['message' => 'register error', 'error' => $error->getMessage()],400);
        }
    }

    public function login(LoginRequest $request) {
        $user = User::where('email', $request->input('email'))->first();

        if(!$user || !Hash::check($request->input('password'), $user->password)) {
            return response()->json(['message' => 'wrong credentials'], 401);
        }

        $token = $user->createToken('romanticunderwear')->plainTextToken;


        $response = [
            'message' => 'success',
            'user' => $user,
            'token' => $token
        ];

        return response()->json([...$response], 201);
    }
}
