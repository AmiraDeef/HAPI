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
        $land_info = $event->land_info;
        Log::info('Received land information: ' . json_encode($land_info));
       // $cropRecommendations = $this->getCropRecommendation($land_info);
      //  $this->sendCropRecommendations($cropRecommendations);
    }
    private function getCropRecommendation($land_info){
        //get the crop recommendation

        $response = Http::post("http://127.0.0.1:5000/recommend", $land_info); //url ai
        if (!$response->successful()) {
            return response()->json(['error' => 'Failed to get crop recommendation.'], $response->status());
        }

        return $response->json();
    }
    private function sendCropRecommendations($cropRecommendations){

        //assuming that the response will be in json and with 3 top crops recommendation
        return response()->json($cropRecommendations);

    }
}
