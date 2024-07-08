<?php

namespace App\Http\Controllers\Website\library;

use App\Http\Controllers\Controller;
use App\Models\Disease;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class DiseaseController extends Controller
{
    public function index()
    {
        $diseases = Disease::all();
        return response()->json(
            $diseases->map(function ($disease) {
                return [
                    'id' => $disease->id,
                    'name' => $disease->name,
                    'crop' => $disease->crop->name,
                    'image' => $disease->image,
                ];
            })
        );
    }

    public function search($id, Request $request)
    {
        try {

            $query = $request->query('query');
            $results = Disease::where('crop_id', $id)
                ->where('name', 'like', "%$query%")
                ->get();

            if ($results->isEmpty()) {
                return response()->json(['message' => 'No results found']);
            }

            return response()->json($results
                ->map(function ($result) {
                    return [
                        'id' => $result->id,
                        'name' => $result->name,
                        'image' => $result->image,
                    ];
                }));
        } catch (QueryException $e) {
            return response()->json(['message' => 'An error occurred while searching']);
        }
    }

    public function show($id, $disease_id)
    {

        $disease = Disease::where('crop_id', $id)->where('id', $disease_id)->first();

        if (!$disease) {
            return response()->json(['message' => 'Disease not found'], 404);
        }

        if ($disease->crop_id != $id) {
            return response()->json(['message' => 'Disease does not belong to the specified crop'], 404);
        }

        return response()->json([
            'id' => $disease->id,
            'name' => $disease->name,
            'image' => $disease->image,
            'causes' => $disease->causes,
            'spread' => $disease->spread,
            'symptoms' => $disease->symptoms,
            'prevention' => $disease->prevention,
            'treatment' => $disease->treatment,
        ]);
    }

}
