<?php

namespace App\Http\Controllers\Land;

use App\Http\Controllers\Controller;
use App\Models\Farmer;
use App\Models\Iot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class LandHistoryController extends Controller
{
    public function history(Request $request)
    {
        $id = $request->query('id');
        if (!$id) {
            return response()->json(['error' => 'ID parameter is missing'], 400);
        }
        $land = Auth::guard('sanctum')->user()->landowner->lands->first();
        if (!$land) {
            return response()->json(['error' => 'Land not found'], 404);
        }
        $land_id = $land->id;
        $comparisonOperator = $id === "1" ? '>=' : '>';
        $iotActions = Iot::where('land_id', $land_id)
            ->where('id', $comparisonOperator, $id)
            ->orderBy('created_at', 'desc')
            ->get();
        $history = [];
        foreach ($iotActions as $action) {
            $history[] = [
                'id' => $action->id,
                'action_type' => $action->action_type,
                'date' => $action->created_at->format('Y-m-d'),
                'time' => $action->created_at->format('H:i A')
            ];
        }
        return response()->json($history);
    }
    public function show($id)
    {
        $land = Auth::guard('sanctum')->user()->landowner->lands->first();

        if (!$land) {
            return response()->json(['error' => 'Land not found'], 404);
        }
        $land_id = $land->id;


        $iotAction = Iot::where('land_id', $land_id)
            ->where('id', $id)
            ->first();

        if (!$iotAction) {
            return response()->json(['error' => 'Action not found'], 404);
        }
        $action = [
            'id' => $iotAction->id,
            'action_type' => $iotAction->action_type,
            'date' => $iotAction->created_at->format('Y-m-d'),
            'time' => $iotAction->created_at->format('H:i A')
        ];
        return response()->json(['action' => $action], 200);
    }

    public function landUpdates()
    {
        $land = Auth::guard('sanctum')->user()->landowner->lands->first();
        if (!$land) {
            return response()->json(['error' => 'Land not found'], 404);
        }

        $land_id = $land->id;
        $latest_fertilization = Iot::where('land_id', $land_id)
            ->where('action_type', 'fertilization')
            ->orderBy('created_at', 'desc')
            ->first();
        $latest_irrigation = Iot::where('land_id', $land_id)
            ->where('action_type', 'irrigation')
            ->orderBy('created_at', 'desc')
            ->first();
        if (!$latest_fertilization || !$latest_irrigation) {
            return response()->json(null);
        }
        $npk = json_decode($latest_fertilization->data);
        $npk = [
            'N' => $npk->N_level,
            'P' => $npk->P_level,
            'K' => ($npk->K_level)
        ];
        $water_level = json_decode($latest_irrigation->data);
        return response()->json([
            'water_level' => $water_level->water_level, 'npk' => $npk
        ]);
    }

    //last farmer
    public function latestFarmer()
    {
        $land = Auth::guard('sanctum')->user()->landowner->lands->first();
        if (!$land) {
            return response()->json([]);
        }
        $land_id = $land->id;
        $farmer = Farmer::where('land_id', $land_id)
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$farmer) {
            return response()->json([]);
        }

        //return username
        return response()->json([
            'username' => $farmer->user->username,
            'date' => $farmer->created_at->format('Y-m-d'),
            'time' => $farmer->created_at->format('H:i A')
        ], 200);
    }


    //    reset land history
    public function reset()
    {
        $land = Auth::guard('sanctum')->user()->landowner->lands->first();
        if (!$land) {
            return response()->json(['error' => 'Land not found'], 404);
        }
        $land_id = $land->unique_land_id;

        Iot::where('land_id', $land_id)->delete();
        return response()->json(['message' => 'Land history reset'], 200);
    }
}
