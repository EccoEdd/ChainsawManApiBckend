<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChainsawControllers\BranchController;
use App\Http\Controllers\ChainsawControllers\TeamController;
use App\Http\Controllers\ChainsawControllers\DemonController;
use App\Http\Controllers\ChainsawControllers\CharacterController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;

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
        Route::put('update/{id}',   [DemonController::class, 'updateDemon'])->where('id', '[0-9]+');
        Route::delete('delete/{id}',[DemonController::class, 'deleteDemon'])->where('id', '[0-9]+');
    });

    Route::get('demons/read',    [DemonController::class, 'readDemons']);
    Route::get('characters/read',[CharacterController::class, 'readCharacters']);

    Route::middleware(['role:a,u'])->prefix('characters')->group(function(){
        Route::post('create',       [CharacterController::class, 'createCharacter']);
        Route::put('update/{id}',   [CharacterController::class, 'updateCharacter'])->where('id', '[0-9]+');
        Route::delete('delete/{id}',[CharacterController::class, 'deleteCharacter'])->where('id', '[0-9]+');
    });

});

Route::prefix('user')->group(function() {

    Route::post('register', [UserController::class, 'createUser']);

    Route::post('logIn', [UserController::class, 'logIn']);

    Route::middleware(['auth:sanctum', 'active'])->group(function(){
        Route::delete('logOut', [UserController::class, 'logOut']);
        Route::get('check', [UserController::class, 'roleCheck']);

        Route::get('role', [UserController::class, 'getRole']);

        Route::middleware(['role:a'])->group(function (){
            Route::get('get', [UserController::class, 'checkUsers']);
            Route::put('update/{id}', [UserController::class, 'modifyUser'])->where('id', '[0-9]+');
            Route::delete('delete/{id}', [UserController::class, 'deleteUser'])->where('id', '[0-9]+');
        });

    });

    Route::middleware('signed')->prefix('verify')->group(function(){
        Route::get('/{id}', [UserController::class, 'sendCodeAndVerifyLink'])
            ->where('id', '[0-9]+')
            ->name('link');

        Route::post('/number/{id}', [UserController::class, 'number'])
            ->name('number');
    });
});

Route::middleware(['auth:sanctum', 'active', 'role:a'])->prefix('roles')->group(function(){
    Route::get('get', [RoleController::class, 'getRoles']);
    Route::post('create', [RoleController::class, 'createRole']);
    Route::put('update/{id}', [RoleController::class, 'updateRole'])->where('id', '[0-9]+');
    Route::delete('delete/{id}', [RoleController::class, 'deleteRole'])->where('id', '[0-9]+');
});
