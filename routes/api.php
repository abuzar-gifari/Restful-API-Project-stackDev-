<?php

use App\Http\Controllers\APIController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// custom routes
Route::get('/users/{id?}',[APIController::class,'getUsers']);
Route::get('/users-list',[APIController::class,'getUsersList']);
Route::post('/add-users',[APIController::class,'addUsers']);
Route::post('/add-multiple-users',[APIController::class,'addMultipleUsers']);
Route::put('/update-user-details/{id?}',[APIController::class,'UpdateUserDetails']);
Route::patch('/update-user-name/{id}',[APIController::class,'UpdateUserName']);
Route::delete('/delete-user/{id}',[APIController::class,'DeleteUser']);

Route::post('register-user',[APIController::class,'RegisterUser']);
Route::post('login-user',[APIController::class,'LoginUser']);
Route::post('logout-user',[APIController::class,'LogoutUser']);