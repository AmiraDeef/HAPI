<?php /** @noinspection ALL */

namespace App\Http\Controllers\Crop;

use App\Events\LandInformationReceived;
use App\Http\Controllers\Controller;
use App\Models\Crop;
use App\Models\Iot;
use App\Models\CropLandHistory;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;



class RecommendationController extends Controller
{
    private $landInfo;
    public function recommend(Request $request){

        if(! Auth::user()->role == 'landowner'){
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $land = Auth::user()->landowner->lands()->first();
        $iot_data = Iot::where('land_id', $land->id)->latest()->first();
        if (!$iot_data) {
            return response()->json(['error' => 'No IoT data found for the specified land'], 404);
        }
        try {
//            $response = Http::post('https://e376e3b7-2a57-4420-9342-3717ad9cec0a.mock.pstmn.io/land-info', [
//                'land_id' => $land->unique_land_id,
//            ]);
            $cropRecommendations = $this->getCropRecommendation($iot_data->data);
            // Return the crop recommendations
            return response()->json($cropRecommendations, 200);

        } catch (\Exception $e) {

            Log::error('Error processing HTTP request: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return response()->json(['error' => 'Failed to process HTTP request'], 500);
        }

    }

    private function getCropRecommendation($landInfo)
    {
        // Convert JSON string to associative array
        $data = json_decode($landInfo, true);
        if ($data === null) {
            return response()->json(['error' => 'Invalid land information format.'], 400);
        }

        $response = Http::post("http://127.0.0.1:5000/get-crop-recommendation", $data);

        if (!$response->successful()) {
            return response()->json(['error' => 'Failed to get crop recommendation.'], $response->status());
        }

        return $response->json();
    }

}
