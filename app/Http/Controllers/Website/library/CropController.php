<?php

namespace App\Http\Controllers\Website\library;

use App\Http\Controllers\Controller;
use App\Models\Crop;
use Illuminate\Http\Request;

class CropController extends Controller
{
    public function index()
    {
        $crops = Crop::all();

        return response()->json($crops->map(function ($crop) {
            return [
                'id' => $crop->id,
                'name' => $crop->name,
            ];
        }));

    }

    public function show($id)
    {
        $crop = Crop::with('diseases')->find($id);
        if (!$crop) {
            return response()->json(['message' => 'Crop not found'], 404);
        }
        $diseases = $crop->diseases->map(function ($disease) {
            return [
                'id' => $disease->id,
                'name' => $disease->name,
            ];
        });

        return response()->json([$diseases]);
    }

    //search crop by name
    public function search(Request $request)
    {
        $name = $request->query('name');
        if (!$name) {
            return response()->json(['error' => 'Name parameter is missing'], 400);
        }
        $crop = Crop::where('name', 'like', "%$name%")->first();
        if (!$crop) {
            return response()->json(['error' => 'Crop not found'], 404);
        }
        return response()->json($crop);
    }

}
