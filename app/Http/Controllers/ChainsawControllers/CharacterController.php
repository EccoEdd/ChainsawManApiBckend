<?php

namespace App\Http\Controllers\ChainsawControllers;

use App\Http\Controllers\Controller;
use App\Models\Character;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CharacterController extends Controller
{
    public function createCharacter(Request $request){
        $validate = $this->getMake($request);
        if($validate->fails())
            return response()->json(['message' => 'unsuccessful...','errors' => $validate->errors()], 403);
        $character = new Character();
        $character->name = $request->name;
        $character->l_name = $request->l_name;
        $character->type = $request->type;
        $character->age = $request->age;
        $character->team_id = $request->id;
        $character->save();
        return response()->json(['message' => 'success...', 'data' => $character], 201);
    }
    public function readCharacters(){
        $characters = Character::with('team')->get();
        return response()->json(['message' => 'all the data...', 'data' => $characters], 202);
    }
    public function updateCharacter(Request $request, int $id){
        $character = Character::find($id);
        if(!$character)
            return response()->json(['message' => 'error 404 not found'], 404);

        $validate = $this->getMake($request);
        if($validate->fails())
            return response()->json(['message' => 'unsuccessful...','errors' => $validate->errors()], 403);

        $oldCharacter = Character::find($id);

        $character->name = $request->name;
        $character->l_name = $request->l_name;
        $character->type = $request->type;

        if($request->alive)
            $character->alive = $request->alive;
        else
            $character->alive = true;

        $character->age = $request->age;
        $character->team_id = $request->id;
        $character->save();

        return response()->json(['message' => 'success...', 'oldData' => $oldCharacter, 'newData' => $character], 202);

    }
    public function deleteCharacter(int $id){
        $character = Character::find($id);
        if(!$character)
            return response()->json(['message' => 'error 404 not found'], 404);
        $character->delete();
        return response()->json(['message' => 'Data deleted', 'data' => $character], 202);
    }

    private function getMake(Request $request): \Illuminate\Validation\Validator
    {
        return Validator::make($request->all(), [
            'name' => 'required|max:15',
            'l_name' => 'max:15',
            'type' => 'required|max:20',
            'alive' => 'boolean',
            'age' => 'max:99999|integer',
            'id' => 'required|exists:teams'
        ], [
            'name' => [
                'required' => 'You need a name for your character',
                'max' => 'You only have 15 characters long'
            ],
            'l_name' => [
                'max' => 'You only have 15 characters long'
            ],
            'type' => [
                'required' => 'You need to specify the type',
                'max' => 'You only have 20 characters long'
            ],
            'alive' => [
                'boolean' => 'This needs to be true or false'
            ],
            'age' => [
                'max' => 'Well We don\'t know actually how long their age could be so you have 99999',
                'integer' => 'It must be a number'
            ],
            'id' => [
                'required' => 'You need to set the team no matter if it is bad or good',
                'exists' => 'It need to exists in the Teams table'
            ]
        ]);
    }
}
