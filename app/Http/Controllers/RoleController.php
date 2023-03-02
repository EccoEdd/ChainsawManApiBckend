<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
{

    public function getRoles(){
        $roles = Role::all();
        return response()->json(['message' => 'all the data...', 'data' => $roles], 202);
    }

    public function createRole(Request $request){
        $validate = Validator::make($request->all(),[
            'role'      => 'required|max:1|unique:roles',
            'description'  => 'required|max:35|unique:roles'
        ]);

        if($validate->fails())
            return response()->json(['message' => 'unsuccessful...','errors' => $validate->errors()], 400);

        $role = new Role();
        $role->role = $request->role;
        $role->description = $request->description;
        $role->save();

        return response()->json(['message' => 'success...', 'data' => $role]);
    }

    public function updateRole(Request $request, int $id){
        if($id == 1 || $id == 2)
            return response()->json(['message' => 'this one is a no',], 200);
        $role = Role::find($id);
        if(!$role)
            return response()->json(['message' => 'error 404 not found'], 404);
        $validate = Validator::make($request->all(),[
            'role'      => 'required|max:1|unique:roles,role,'.$id,
            'description'  => 'required|max:35|unique:roles,description,'.$id
        ]);

        if($validate->fails())
            return response()->json(['message' => 'unsuccessful...','errors' => $validate->errors()], 400);

        $role->role = $request->role;
        $role->description = $request->description;
        $role->save();

        return response()->json(['message' => 'success...'], 202);
    }

    public function deleteRole(int $id){
        if($id == 1 || $id == 2)
            return response()->json(['message' => 'forbidden',], 400);
        $role = Role::find($id);
        $role->delete();
        return response()->json(['message' => 'deleted']);
    }
}
