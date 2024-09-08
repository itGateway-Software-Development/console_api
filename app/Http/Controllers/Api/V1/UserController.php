<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
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
}
