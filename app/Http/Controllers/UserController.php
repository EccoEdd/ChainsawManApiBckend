<?php

namespace App\Http\Controllers;

use App\Jobs\FirstAuthMailSender;
use App\Jobs\SecondAuthMailSender;
use App\Jobs\SmsSender;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Nette\Utils\Random;
use Illuminate\Support\Facades\URL;

class UserController extends Controller
{
    public function createUser(Request $request){
        $validation = Validator::make($request->all(), [
            'user'  => 'required',
            'email' => 'required|unique:users|email:rfc,dns',
            'phone' => 'required|unique:users|numeric|digits:10',
            'password' => 'required'
        ]);
        if($validation->fails())
            return response()->json(['message' => 'unsuccessful...','errors' => $validation->errors()], 400);

        $user = new User();

        $user->name = $request->user;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->password = Hash::make($request->password);
        $user->code = random_int(100000, 999999);
        $user->role_id = 2;

        $user->save();
        $url = URL::temporarySignedRoute('link', now()->addMinutes(30), ['id' => $user->id]);
        $urlNumber = URL::temporarySignedRoute('number', now()->addMinutes(30), ['id' => $user->id]);
        FirstAuthMailSender::dispatch($user, $url)->delay(now()->addSeconds(2));

        return response()->json([
            'message' => 'please check your Mail to continue',
            'url'     =>  $urlNumber
            ]);
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


    public function sendCodeAndVerifyLink(Request $request, int $id){
        if(!$request->hasValidSignature())
            abort(401);
        $user = User::find($id);
        if(!$user)
            return response()->json(['message' => 'error 404 not found'], 404);
        if($user->active)
            return response()->json(['message' => 'User already verified'], 304);
        //$url = URL::temporarySignedRoute('number', now()->addMinutes(30), ['id' => $user->id]);

        $url = 'http://localhost:4200/codeSender';

        SmsSender::dispatch($user);
        SecondAuthMailSender::dispatch($user, $url);

        $signedRoute = URL::signedRoute('viewPhone', now()->addMinutes(2));
        return redirect()->to($signedRoute);

        //return response()->json(['message' => 'please check your phone'], 200);
    }

    public function number(Request $request, int $id){
        if(!$request->hasValidSignature())
            abort(401);
        $validate = Validator::make($request->all(),[
           'code' => 'required'
        ]);
        if($validate->fails())
            return response()->json(['message' => 'unsuccessful...','errors' => $validate->errors()], 400);

        $user = User::find($id);

        if(!$user)
            return response()->json(['message' => 'error 404 not found'], 404);
        if($user->active)
            return response()->json(['message' => 'User already verified'], 304);
        if($user->code != $request->code)
            return response()->json(['message' => 'unsuccessful...'], 400);
        $user->active = true;
        $user->save();
        return response()->json(['message' => 'Welcome'], 200);
    }

    public function roleCheck(Request $request){
        $user = $request->user();
        if(!$user)
            return response()->json(['role' => false]);
        $role = $user->role;
        return response()->json(['role' => $role->role]);
    }

    public function checkUsers(){
        $users = User::with('role')->get();
        return response()->json(['message' => 'all the data...', 'data' => $users], 202);
    }

    public function modifyUser(Request $request, int $id){
        $user = User::find($id);
        if(!$user)
            return response()->json(['message' => 'error 404 not found'], 404);

        $validation = Validator::make($request->all(), [
            'user'  =>  'required',
            'email' =>  'required|unique:users,email,'.$id.'|email:rfc,dns',
            'phone' =>  'required|unique:users,phone,'.$id.'|numeric',
            'status' => 'required|boolean|'.($id == 1 ? 'in:1' : ''),
            'id'    =>  'required|exists:roles|'.($id == 1 ? 'in:1' : '')
        ], [
            'id.required' =>  'You need the role',
            'status.in' => 'You cannot change the status for this one',
            'id.in' => 'You cannot change the rol for this one',
        ]);
        if($validation->fails())
            return response()->json(['message' => 'unsuccessful...','errors' => $validation->errors()], 400);

        $user->name = $request->user;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->active = $request->status;
        $user->role_id = $request->id;

        $user->save();

        return response()->json(['message' => 'success...'], 202);
    }

    public function deleteUser(int $id){
        if($id == 1)
            return response()->json(['message' => 'this one is a no'], 200);

        $user = User::find($id);
        $user->delete();

        return response()->json(['message' => 'deleted']);
    }

    public function getRole(Request $request){
        return response()->json(['role' => $request->user()->role]);
    }

}
