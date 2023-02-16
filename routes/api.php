<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChainsawControllers\BranchController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('chainsaw')->group(function(){

    Route::prefix('branches')->group(function(){
        Route::post('create',       [BranchController::class, 'createBranch']);
        Route::get('read',          [BranchController::class, 'readBranches']);
        Route::put('update/{id}',   [BranchController::class, 'updateBranch'])->where('id', '[0-9]+');
        Route::delete('delete/{id}',[BranchController::class, 'deleteBranch'])->where('id', '[0-9]+');
    });

});
