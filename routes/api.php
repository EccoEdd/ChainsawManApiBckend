<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChainsawControllers\BranchController;
use App\Http\Controllers\ChainsawControllers\TeamController;
use App\Http\Controllers\ChainsawControllers\DemonController;
use App\Http\Controllers\ChainsawControllers\CharacterController;
use App\Http\Controllers\UserController;

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

Route::middleware(['auth:sanctum', 'active'])->prefix('chainsaw')->group(function(){

    Route::middleware(['role:a'])->prefix('branches')->group(function(){
        Route::post('create',       [BranchController::class, 'createBranch']);
        Route::get('read',          [BranchController::class, 'readBranches']);
        Route::put('update/{id}',   [BranchController::class, 'updateBranch'])->where('id', '[0-9]+');
        Route::delete('delete/{id}',[BranchController::class, 'deleteBranch'])->where('id', '[0-9]+');
    });

    Route::middleware(['role:a'])->prefix('teams')->group(function(){
        Route::post('create',       [TeamController::class, 'createTeam']);
        Route::get('read',          [TeamController::class, 'readTeams']);
        Route::put('update/{id}',   [TeamController::class, 'updateTeam'])->where('id', '[0-9]+');
        Route::delete('delete/{id}',[TeamController::class, 'deleteTeam'])->where('id', '[0-9]+');
    });

    Route::middleware(['role:a,u'])->prefix('demons')->group(function(){
        Route::post('create',       [DemonController::class, 'createDemon']);
        Route::get('read',          [DemonController::class, 'readDemons']);
        Route::put('update/{id}',   [DemonController::class, 'updateDemon'])->where('id', '[0-9]+');
        Route::delete('delete/{id}',[DemonController::class, 'deleteDemon'])->where('id', '[0-9]+');
    });

    Route::middleware(['role:a,u'])->prefix('characters')->group(function(){
        Route::post('create',       [CharacterController::class, 'createCharacter']);
        Route::get('read',          [CharacterController::class, 'readCharacters']);
        Route::put('update/{id}',   [CharacterController::class, 'updateCharacter'])->where('id', '[0-9]+');
        Route::delete('delete/{id}',[CharacterController::class, 'deleteCharacter'])->where('id', '[0-9]+');
    });

});

Route::prefix('user')->group(function() {
    Route::post('register', [UserController::class, 'createUser']);
    Route::post('logIn', [UserController::class, 'logIn']);
    Route::middleware(['auth:sanctum', 'active'])->group(function(){
        Route::delete('logOut', [UserController::class, 'logOut']);
    });

    Route::middleware('signed')->prefix('verify')->group(function(){
        Route::get('/{id}', [UserController::class, 'sendCodeAndVerifyLink'])
            ->where('id', '[0-9]+')
            ->name('link');
        Route::post('/number', [UserController::class, 'number'])
            ->name('number');
    });
});
