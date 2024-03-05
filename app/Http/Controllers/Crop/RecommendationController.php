<?php /** @noinspection ALL */

namespace App\Http\Controllers\Crop;

use App\Http\Controllers\Controller;
use App\Models\Crop;
use App\Models\CropLandHistory;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use PhpMqtt\Client\Facades\MQTT;
use PhpMqtt\Client\MqttClient;


class RecommendationController extends Controller
{
    private $landInfo;
    public function recommend(Request $request){

        if(! Auth::user()->role == 'landowner'){
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $land = Auth::user()->landowner->land;

        // Subscribe to the topic where land information will be received
        try {
//
            // Subscribe to the topic where land information will be received
                $mqtt=MQTT::connection();
            $mqtt->subscribe(
                "land/info",
                function (string $topic, string $message) use ($land) {
                    $landInfo = json_decode($message, true);
                    if ($landInfo && isset($landInfo['land_id']) && $landInfo['land_id'] === $land->unique_land_id) {
                        // Land information received, dispatch event
                        event(new LandInformationReceived($landInfo['land_info']));
                    }
                }
            );

            //send(publish) mqtt land id to get land info
            $mqttClient->publish(
                "land/{$land->unique_land_id}",
                json_encode([
                    'land_id' => $land->unique_land_id,
                ])
            );
        } catch (\Exception $e) {
            Log::error('Error processing MQTT request: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to process MQTT request'], 500);
        }


//        $startTime = microtime(true);
//        $timeout = 5; // Adjust timeout as needed
//
//        while (microtime(true) - $startTime < $timeout) {
//            if (isset($landInfo) && $landInfo['land_id'] === $land->unique_land_id) {
//                // Land information received, proceed with processing
//                break;
//            }
//            usleep(100000); // Sleep for 100 milliseconds
//        }

        if (!isset($landInfo)) {
            // Handle timeout: return error or retry
            return response()->json(['error' => 'Failed to receive land information within timeout'], 500);
        }

// Land information received, proceed with processing as before


        //return response()->json(['message' => 'Request for recommendation sent. Please wait for response.'], 202);
    }


//    public function recommend(Request $request)
//    {
//        // Check if the user is authorized
//        if (!Auth::user()->role == 'landowner') {
//            return response()->json(['error' => 'Unauthorized'], 401);
//        }
//
//        $land = Auth::user()->landowner->land;
//
//        try {
//            // Create MQTT client instance
//            $mqttClient = new MqttClient();
//            $mqttClient->connect();
//
//            // Subscribe to the topic where land information will be received
//            $mqttClient->subscribe(
//                "land/{$land->unique_land_id}/info",
//                function (string $topic, string $message) use ($land) {
//                    $landInfo = json_decode($message, true);
//                    if ($landInfo && isset($landInfo['land_id']) && $landInfo['land_id'] === $land->unique_land_id) {
//                        // Land information received, dispatch event
//                        event(new LandInformationReceived($landInfo['land_info']));
//                    }
//                }
//            );
//
//            // Publish MQTT land ID to get land info
//                MQTT::publish(
//                "land/{$land->unique_land_id}",
//                json_encode([
//                    'land_id' => $land->unique_land_id,
//                ])
//            );
//        } catch (\Exception $e) {
//            Log::error('Error processing MQTT request: ' . $e->getMessage());
//            return response()->json(['error' => 'Failed to process MQTT request'], 500);
//        }
//
//        // If no land information received, return an error
//        return response()->json(['error' => 'Failed to receive land information within timeout'], 500);
//    }




}
