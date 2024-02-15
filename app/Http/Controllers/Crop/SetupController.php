<?php

namespace App\Http\Controllers\Crop;

use App\Http\Controllers\Controller;
use http\Env\Request;
use Illuminate\Support\Facades\Auth;

class SetupController extends Controller
{
    //selectionManually
    public function selectionManually(Request $request){
        if(! Auth::user()->role == 'landowner'){
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $land_id = Auth::user()->landowner->unique_farm_id;
        $crop = $request->input('crop');
        $land = Farm::where('unique_farm_id', $land_id)->first();
        $land->crop = $crop;





    }
    //recommendationCrop
    public function recommendationCrop(Request $request)
    {


    }
}
