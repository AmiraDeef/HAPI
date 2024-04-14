<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ProfileController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Crop\DetectionController;
use App\Http\Controllers\Crop\RecommendationController;
use App\Http\Controllers\Crop\SelectingManualController;
use App\Http\Controllers\IOT\IotDataController;
use App\Http\Controllers\NotificationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

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

//for guest detection

Route::post('/detect', [DetectionController::class, 'detect'])->middleware('auth_optional:api')->name('detect');


//for iot
Route::post('/iot/land-data', [IotDataController::class, 'store']);
Route::get('/iot/land-data', [IotDataController::class, 'sendLand']);
Route::get('/iot/land-data/{land_id}', [IotDataController::class, 'index']);
Route::put('/iot/land-data/{land_id}', [IotDataController::class, 'update']);
Route::delete('/iot/land-data/{land_id}', [IotDataController::class, 'destroy']);

// Authentication Routes
Route::post('/signup/farmer', [RegisterController::class, 'registerFarmer'])->name('signupFarmer');
Route::post('/signup/landowner', [RegisterController::class, 'registerLandowner'])->name('signupLandowner');
Route::post('/login', [LoginController::class, 'login'])->name('login');

// Authenticated Routes
Route::middleware('auth:sanctum')->group(function () {
    //crop setup
    Route::post('/crop/selecting-manual', [SelectingManualController::class, 'selectionManually']);
    Route::post('/crop/recommendation', [RecommendationController::class, 'recommend']);
    //detection
    Route::post('/detect/history', [DetectionController::class, 'history'])->name('detect.history');
    //for notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications');
    Route::put('/notifications/{id}/read', [NotificationController::class, 'seenNotification'])->name('seen');
    Route::post('/notifications/messages', [NotificationController::class, 'createMessageNotification'])->name('message');
    //profile
    Route::post('/profile/password', [ProfileController::class, 'changePassword']);
    Route::delete('/profile', [ProfileController::class, 'deleteAccount']);
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});
