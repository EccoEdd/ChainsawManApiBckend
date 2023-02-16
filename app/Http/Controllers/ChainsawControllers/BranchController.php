<?php

namespace App\Http\Controllers\ChainsawControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


class BranchController extends Controller
{
    public function createBranch(Request $request){
        $request->validate([
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
        return $request;
    }
    public function readBranches(){

    }
    public function updateBranch(Request $request, int $id){

    }
    public function deleteBranch(int $id){

    }
}
