<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;


class UserController extends Controller
{
    public function createUser(Request $request){

    }

    public function logIn(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);
        if($validator->fails())
            return response()->json(['message' => 'unsuccessful...','errors' => $validator->errors()], 400);
        $user = User::where('email', $request->email)->where('active', true)->first();
        if (! $user || ! Hash::check($request->password, $user->password))
            return response()->json(['message' => 'Try again...'], 401);

        $token = $user->createToken('auth_token')->plainTextToken;
        return response()->json(['message' => 'Welcome','token' => $token], 200);
    }
}
