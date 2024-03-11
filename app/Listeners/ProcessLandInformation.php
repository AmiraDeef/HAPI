<?php
namespace App\Listeners;

use App\Events\LandInformationReceived;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ProcessLandInformation
{
/**
* Create the event listener.
*/
public function __construct()
{
//
}

/**
* Handle the event.
*/
public function handle(LandInformationReceived $event): void
{
$landInfo = $event->land_info;
Log::info('Received land information: ' . json_encode($landInfo));

// Get crop recommendations based on land information
$cropRecommendations = $this->getCropRecommendation($landInfo);

// Send crop recommendations along with the success message
$this->sendCropRecommendations($cropRecommendations);
}

private function getCropRecommendation($landInfo)
{
//get the crop recommendation
$response = Http::post("https://e376e3b7-2a57-4420-9342-3717ad9cec0a.mock.pstmn.io/recommend-crop", $landInfo); //url ai
if (!$response->successful()) {
return response()->json(['error' => 'Failed to get crop recommendation.'], $response->status());
}
return $response->json();
}

private function sendCropRecommendations($cropRecommendations)
{
// Send the response with crop recommendations and success message
return response()->json([
'message' => 'Land information received successfully',
'crop_recommendations' => $cropRecommendations,
], 200);
}
}
