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
                ];
            })
        );
    }

    public function search($id, Request $request)
    {
        try {
//
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
                    ];
                }));
        } catch (QueryException $e) {
            return response()->json(['message' => 'An error occurred while searching']);
        }
    }


    public function show($id)
    {
        $disease = Disease::find($id);
        if (!$disease) {
            return response()->json(['message' => 'Disease not found'], 404);
        }
        return response()->json([
            'id' => $disease->id,
            'name' => $disease->name,
            'causes' => $disease->causes,
            'spread' => $disease->spread,
            'symptoms' => $disease->symptoms,
            'prevention' => $disease->prevention,
            'treatment' => $disease->treatment,

        ]);
    }


}
