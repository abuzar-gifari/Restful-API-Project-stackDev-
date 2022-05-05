<?php

use App\Http\Controllers\APIController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// custom routes
Route::get('/users/{id?}',[APIController::class,'getUsers']);
Route::post('/add-users',[APIController::class,'addUsers']);
Route::post('/add-multiple-users',[APIController::class,'addMultipleUsers']);
