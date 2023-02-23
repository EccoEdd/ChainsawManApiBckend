<?php

namespace App\Http\Controllers\ChainsawControllers;

use App\Http\Controllers\Controller;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TeamController extends Controller
{
    public function createTeam(Request $request){
        $validate = Validator::make($request->all(),[
            'name'      => 'required|max:45|unique:teams',
            'id' => 'required|exists:branches'
        ],[
            'name' => [
                'required' => 'You need a name for your team',
                'max'      => 'You only have 45 characters long',
                'unique'   => 'This team already exists'
            ],
            'branch_id'    => [
                'required' => 'You need a branch to link your team',
                'exits'    => 'The branch must exists'
            ]
        ]);

        if($validate->fails())
            return response()->json(['message' => 'unsuccessful...','errors' => $validate->errors()], 400);

        $team = new Team();
        $team->name = $request->name;
        $team->branch_id = $request->id;
        $team->save();

        return response()->json(['message' => 'success...', 'data' => $team], 201);
    }
    public function readTeams(){
        $teams = Team::with('branch')->get();
        return response()->json(['message' => 'all the data...', 'data' => $teams], 202);
    }
    public function updateTeam(Request $request, int $id){
        $team = Team::find($id);

        if(!$team)
            return response()->json(['message' => 'error 404 not found'], 404);

        $validate = Validator::make($request->all(),[
            'name' => 'required|max:45|unique:teams,name,'.$id,
            'status' => 'required|boolean',
            'id' => 'required|exists:branches'
        ],[
            'name' => [
                'required' => 'You need a name for your branch',
                'max'      => 'You only have 45 characters long',
                'unique'   => 'This name is being already occupied'
            ],
            'status' => [
                'required' => 'You need to set this one',
                'boolean'  => 'It has to be True or False'
            ],
            'branch_id'    => [
                'required' => 'You need a branch to link your team',
                'exits'    => 'The branch must exists'
            ]
        ]);

        if($validate->fails())
            return response()->json(['message' => 'unsuccessful...','errors' => $validate->errors()], 400);

        $oldTeam = Team::find($id);
        $team->name = $request->name;
        $team->status = $request->status;
        $team->id = $request->id;
        $team->save();

        return response()->json(['message' => 'success...', 'oldData' => $oldTeam, 'newData' => $team], 202);
    }
    public function deleteTeam(int $id){
        $team = Team::find($id);

        if(!$team)
            return response()->json(['message' => 'error 404 not found'], 404);

        $team->delete();
        return response()->json(['message' => 'Data deleted', 'data' => $team], 202);
    }
}
