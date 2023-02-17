<?php

namespace App\Http\Controllers\ChainsawControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Demon;

class DemonController extends Controller
{
    public function createDemon(Request $request){
        $validate = Validator::make($request->all(),[
            'name'      => 'required|max:45|unique:demons',
            'category'  => 'required'
        ],[
            'name' => [
                'required' => 'You need a name for your demon',
                'max'      => 'You only have 45 characters long',
                'unique'   => 'This demon already exists'
            ],
            'category'    => [
                'required' => 'You need to classify this demon'
            ]
        ]);

        if($validate->fails())
            return response()->json(['message' => 'unsuccessful...','errors' => $validate->errors()], 403);

        $demon = new Demon();
        $demon->name = $request->name;
        $demon->category = $request->category;
        $demon->save();

        return response()->json(['message' => 'success...', 'data' => $demon], 201);

    }
    public function readDemons(){
        $demons = Demon::all();
        return response()->json(['message' => 'all the data...', 'data' => $demons], 202);
    }
    public function updateDemon(Request $request, int $id){
        $demon = Demon::find($id);
        if(!$demon)
            return response()->json(['message' => 'error 404 not found'], 404);

        $validate = Validator::make($request->all(),[
            'name' => 'required|max:45|unique:demons,name,'.$id,
            'category'  => 'required'
        ],[
            'name' => [
                'required' => 'You need a name for your demon',
                'max'      => 'You only have 45 characters long',
                'unique'   => 'This demon already exists'
            ],
            'category'    => [
                'required' => 'You need to classify this demon'
            ]
        ]);

        if($validate->fails())
            return response()->json(['message' => 'unsuccessful...','errors' => $validate->errors()], 403);

        $oldDemon = Demon::find($id);
        $demon->name = $request->name;
        $demon->category = $request->category();
        $demon->save();

        return response()->json(['message' => 'success...', 'oldData' => $oldDemon, 'newData' => $demon], 202);
    }
    public function deleteDemon(int $id){
        $demon = Demon::find($id);

        if(!$demon)
            return response()->json(['message' => 'error 404 not found'], 404);

        $demon->delete();
        return response()->json(['message' => 'Data deleted', 'data' => $demon], 202);
    }
}
