<?php /** @noinspection ALL */

namespace App\Http\Controllers\Crop;

use App\Http\Controllers\Controller;
use App\Models\Crop;
use App\Models\CropLandHistory;
use http\Message;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use carbon\carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use PhpMqtt\Client\MqttClient;
use PhpMqtt\Client\Facades\MQTT;

class SelectingManualController extends Controller
{
    //check if the user is a landowner
    public function selectionManually(Request $request): JsonResponse
    {
        $request->validate([
            'crop' => 'required|string'
        ]);
        if (Auth::user()->role !== 'landowner') {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        //get the land of the landowner
        $land = Auth::user()->landowner->land;
        $existing_history = CropLandHistory::where('land_id', $land->id)
            ->orderBy('planted_at', 'desc')
            ->first();
        if ($existing_history) {
            return response()->json(['message' => 'Duplicate request received. Please try again later.'], 429);
        }
        $crop = Crop::findOrCreate(['name' => $request->input('crop')]);

        CropLandHistory::create([
            'land_id' => $land->id,
            'crop_id' => $crop->id,
            'planted_at' => Carbon::now(),
        ]);
        $land->crop_id = $crop->id;
        $land->save();

        // Send the chosen crop and land ID via http request
            try {
                $response = Http::post('https://your-iot-api.com/selected-crop', [
                    'land_id' => $land->unique_land_id,
                    'crop' => $crop->name,
                ]);
                if ($response->successful()) {
                    Log::error('Error sending crop selection data to IoT API:', ['response' => $response]);
                } else {
                    //just for test ,I'll refactor it later..
                    switch ($response->status()) {
                        case 400:
                            return response()->json(['error' => 'Bad request'], 400);
                        case 404:
                            return response()->json(['error' => 'crop information not found'], 404);
                        case 500:
                            return response()->json(['error' => 'Server error'], 500);
                        default:
                            return response()->json(['error' => 'Failed to send choosen crop information'], $response->status());
                    }
                }
            } catch (\Exception $e) {
                Log::error('Error processing HTTP request: ' . $e->getMessage());
                Log::error($e->getTraceAsString());
                return response()->json(['error' => 'Failed to process HTTP request'], 500);
            }

            return response()->json(['message' => "the chosen crop is " . $crop->name], 200);


        }




}



