<?php /** @noinspection ALL */

namespace App\Http\Controllers\Crop;

use App\Http\Controllers\Controller;
use App\Models\Crop;
use App\Models\CropLandHistory;
use carbon\carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $land = Auth::user()->landowner->lands->first();
//        $existing_history = CropLandHistory::where('land_id', $land->id)
//            ->orderBy('planted_at', 'desc')
//            ->first();
//        if ($existing_history) {
//            return response()->json(['message' => 'Duplicate request received. Please try again later.'], 429);
//        }
        $cropName = $request->input('crop');
        $crop = Crop::where('name', $cropName)->firstOrFail();

        CropLandHistory::create([
            'land_id' => $land->id,
            'crop_id' => $crop->id,
            'planted_at' => Carbon::now(),
        ]);
        $land->crop_id = $crop->id;
        $land->save();

        return response()->json(null)->setStatusCode(200);


    }

}



