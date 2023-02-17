<?php

namespace App\Http\Controllers\ChainsawControllers;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Validator;


class BranchController extends Controller
{
    public function createBranch(Request $request){
        $validate = Validator::make($request->all(),[
            'name' => 'required|max:45|unique:branches',
            'location' => 'required|unique:branches'
        ],[
            'name' => [
                'required' => 'You need a name for your branch',
                'max'      => 'You only have 45 characters long',
                'unique'   => 'This branch already exists'
            ],
            'location' => [
                'required' => 'You need a location for this branch',
                'unique'   => 'You can\'t have two or more branches in the same spot'
            ]
        ]);

        if($validate->fails())
            return response()->json(['message' => 'unsuccessful...','errors' => $validate->errors()], 403);

        $branch = new Branch();
        $branch->name     = $request->name;
        $branch->location = $request->location;
        $branch->save();

        return response()->json(['message' => 'success...', 'data' => $branch], 201);
    }
    public function readBranches(){
        $branches = Branch::all();
        return response()->json(['message' => 'all the data...', 'data' => $branches], 202);
    }
    public function updateBranch(Request $request, int $id){
        $branch = Branch::find($id);
        if(!$branch)
            return response()->json(['message' => 'error 404 not found'], 404);

        $validate = Validator::make($request->all(),[
            'name' => 'required|max:45|unique:branches,name,'.$id,
            'location' => 'required|unique:branches,location,'.$id
        ],[
            'name' => [
                'required' => 'You need a name for your branch',
                'max'      => 'You only have 45 characters long',
                'unique'   => 'This name is being already occupied'
            ],
            'location' => [
                'required' => 'You need a location for this branch',
                'unique'   => 'This location is being already occupied'
            ]
        ]);

        if($validate->fails())
            return response()->json(['message' => 'unsuccessful...','errors' => $validate->errors()], 403);

        $oldBranch = Branch::find($id);
        $branch->name     = $request->name;
        $branch->location = $request->location;
        $branch->save();

        return response()->json(['message' => 'success...', 'oldData' => $oldBranch, 'newData' => $branch], 202);
    }
    public function deleteBranch(int $id){
        $branch = Branch::find($id);

        if(!$branch)
            return response()->json(['message' => 'error 404 not found'], 404);

        $branch->delete();
        return response()->json(['message' => 'Data deleted', 'data' => $branch], 202);
    }
}
