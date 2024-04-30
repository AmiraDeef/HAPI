<?php /** @noinspection ALL */

namespace App\Http\Controllers\Crop;

use App\Http\Controllers\Controller;
use App\Models\Iot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;


class RecommendationController extends Controller
{
    private $landInfo;
    public function recommend(Request $request){

        if (!Auth::user()->role == 'landowner') {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $land = Auth::user()->landowner->lands()->first();
        $latest_fertilization = Iot::where('land_id', $land->id)
            ->where('action_type', 'fertilization')
            ->latest()
            ->first();
        //dd($latest_fertilization);
        if (!$latest_fertilization) {
            return response()->json(['error' => 'No data found for the specified land'], 404);
        }
        $npk = json_decode($latest_fertilization->data);
        $npk = [
            'N' => (int)$npk->N,
            'P' => (int)$npk->P,
            'K' => (int)$npk->K
        ];
        try {
//            $response = Http::post('https://e376e3b7-2a57-4420-9342-3717ad9cec0a.mock.pstmn.io/land-info', [
//                'land_id' => $land->unique_land_id,
//            ]);
            $cropRecommendations = $this->getCropRecommendation($npk);
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
//        $data = json_decode($landInfo, true);
        $data = $landInfo;
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
