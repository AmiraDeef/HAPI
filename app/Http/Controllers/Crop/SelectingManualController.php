<?php

namespace App\Http\Controllers\Crop;

use App\Http\Controllers\Controller;
use App\Models\Crop;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use carbon\carbon;

class SelectingManualController extends Controller
{
    //check if the user is a landowner
    public function selectionManually(Request $request): JsonResponse
    {
        $request->validate([
            'crop'=>'required|string'
        ]);
        if(! Auth::user()->role == 'landowner'){
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $crop=Crop::findOrCreate(['crop_name'=>$request->input('crop')]);
        //get the land of the landowner
        $land = Auth::user()->landowner->land;
        LandCropHistory::create([
            'land_id' => $land->id,
            'crop_id' => $crop->id,
            'start_date' => Carbon::now(),
        ]);
        $land->crop_id = $crop->id;
        $land->save();

        //send to iot using mqtt
        $this->updateToIot($land->unique_land_id, $crop->crop_name);

        //when receiving info crop npk from iot ,then add them to response
        return response()->json('the chosen crop is'.$crop);


    }

    public function store(Request $request)
    {
        //saving the crop in db


    }
     public function updateToIot($land_id, $crop){


    }

}



