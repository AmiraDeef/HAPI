<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ProfileController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Crop\DetectionController;
use App\Http\Controllers\Crop\RecommendationController;
use App\Http\Controllers\Crop\SelectingManualController;
use App\Http\Controllers\IOT\IotDataController;
use App\Http\Controllers\Land\LandHistoryController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Website\ContactController;
use App\Http\Controllers\Website\library\CropController;
use App\Http\Controllers\Website\library\DiseaseController;
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

//Route::post('/land', function (Request $request) {
//    // Access the land_id from the request body (assuming JSON format)
//    $landId = $request->json('land_id') !== null ? $request->json('land_id') : null;
//
//    if (!$landId) {
//        return response()->json(['error' => 'Missing land_id in request body'], 400); // Handle missing land_id
//    }
//    return response()->json(['land_id' => $landId], 200);
//
//});


//for iot
Route::post('/iot/land-data', [IotDataController::class, 'store']);
Route::get('/iot/land', [IotDataController::class, 'sendLand'])->name('iot.land');
Route::post('/iot/land/crop', [IotDataController::class, 'cropLand'])->name('iot.crop');
//Route::get('/iot/land-data/{land_id}', [IotDataController::class, 'index']);
//Route::put('/iot/land-data/{land_id}', [IotDataController::class, 'update']);
//Route::delete('/iot/land-data/{land_id}', [IotDataController::class, 'destroy']);

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
    Route::get('/detections/me', [DetectionController::class, 'myDetections'])->name('detect.my');
    Route::get('/detections', [DetectionController::class, 'history'])->name('detect.history');
    Route::get('/detections/{id}', [DetectionController::class, 'show'])->name('detect.show');
    //    Route::delete('/detections', [DetectionController::class, 'resetDetectionHistory'])->name('detect.delete');

    //land Actions history
    Route::get('/land/history/{id}', [LandHistoryController::class, 'show'])->name('land.show');
    Route::get('/land/history', [LandHistoryController::class, 'history'])->name('land.history');
    Route::get('/land/data', [LandHistoryController::class, 'landUpdates'])->name('land.data');
    Route::get('land/latestFarmer', [LandHistoryController::class, 'latestFarmer'])->name('latest.farmer');
    //Route::delete('/land/history', [LandHistoryController::class, 'reset'])->name('land.delete-history');

    //for notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications');
    Route::put('/notifications/{id}/read', [NotificationController::class, 'seenNotification'])->name('seen');
    Route::post('/notifications/messages', [NotificationController::class, 'createMessageNotification'])->name('message');
    //profile
    Route::post('/setting/password', [ProfileController::class, 'changePassword']);
    Route::post('/settings/check-password', [ProfileController::class, 'check_password']);
    Route::delete('/settings/account', [ProfileController::class, 'deleteAccount']);
    Route::get('/settings/farmers', [ProfileController::class, 'listFarmers']);
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});

//for guest detection

Route::post('/detect', [DetectionController::class, 'detect'])->middleware('auth_optional:sanctum')->name('detect');


//for website


Route::post('/contact', [ContactController::class, 'store']);

Route::get('/crops', [cropController::class, 'index'])->name('crops');
Route::get('/crops/{id}', [CropController::class, 'show'])->name('crop.show');
Route::get('/crops/{id}/diseases', [CropController::class, 'show_diseases'])->name('crop.diseases');

Route::get('/crops/{id}/diseases/search', [DiseaseController::class, 'search'])->name('diseases.search');
Route::get('/diseases', [DiseaseController::class, 'index'])->name('diseases');
Route::get('/crops/{id}/diseases/{disease_id}', [DiseaseController::class, 'show'])->name('crop.disease');
