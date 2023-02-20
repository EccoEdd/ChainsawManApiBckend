<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;


class UserController extends Controller
{
    public function createUser(Request $request){
        $validation = Validator::make($request->all(), [
            'user'  => 'required',
            'email' => 'required|unique:users|email:rfc,dns',
            'phone' => 'required|unique:users|size:10|numeric',
            'password' => 'required'
        ]);
        if($validation->fails())
            return response()->json(['message' => 'unsuccessful...','errors' => $validation->errors()], 403);

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);

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
            return response()->json(['message' => 'incorrect User or Password'], 401);

        $token = $user->createToken('auth_token')->plainTextToken;
        return response()->json(['message' => 'Welcome','token' => $token], 200);
    }

    public function logOut(Request $request){
        $request->user()->tokens()->delete();
        return response()->json(['message' => 'Good Bye'], 200);
    }
}
