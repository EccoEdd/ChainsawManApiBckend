<?php

namespace App\Http\Controllers\Normal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NormalController extends Controller
{
    public function viewCheck(Request $request){
        if(!$request->hasValidSignature())
            abort(401);
        return view('checkPhone');
    }
}
