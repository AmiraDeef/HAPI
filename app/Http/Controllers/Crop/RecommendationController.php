<?php /** @noinspection ALL */

namespace App\Http\Controllers\Crop;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use PhpMqtt\Client\Facades\MQTT;

class RecommendationController extends Controller
{
    public function recommend(Request $request){
        if(! Auth::user()->role == 'landowner'){
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $land = Auth::user()->landowner->land;
        //send mqtt lad id to get land info
        try{
            MQTT::publish(
                "land/{$land->unique_land_id}",
                json_encode([
                    'land_id' => $land->unique_land_id,

                ])
            );
        }catch (\Exception $e) {
            Log::error('Error publishing MQTT message: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to publish MQTT message'], 500);
        }

        // Subscribe to the topic where land information will be received







    }


}
