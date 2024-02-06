<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

//for guest -->just detection


//for authenticated user
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/signup/farmer',[RegisterController::class,"registerFarmer"])->name('signupFarmer');
Route::post('/signup/landowner',[RegisterController::class,"registerLandowner"])->name('signupLandowner');
Route::post('/login',[LoginController::class,"login"])->name('login');
Route::post('/logut',[LoginController::class,"logout"])->name('logout');

