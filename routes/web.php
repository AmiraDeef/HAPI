<?php

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Client\Request;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
// routes/web.php
Route::post('/mock-iot', function (Request $request) {
    $landId = $request->input('land_id');
    // Log the received land_id or respond with a success message
    Log::info("Received land ID in mock IoT endpoint: {$landId}");
    return response()->json(['message' => $landId.' Land ID received successfully']);
});

