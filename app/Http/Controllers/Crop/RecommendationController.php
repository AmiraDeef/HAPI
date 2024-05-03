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
            $cropRecommendations = $this->getCropRecommendation($npk);
            $sortedRecommendations = collect($cropRecommendations)->sortBy(function ($value, $key) {
                return $value;
            })->toArray();
            return response()->json($sortedRecommendations, 200);

        } catch (\Exception $e) {

            Log::error('Error processing HTTP request: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return response()->json(['error' => 'Failed to process HTTP request'], 500);
        }
    }

    private function getCropRecommendation($landInfo)
    {
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
