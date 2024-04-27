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
        //dd($land_id, $id);
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
    //latest action
    public function latestAction()
    {
        $land = Auth::guard('sanctum')->user()->landowner->lands->first();
        if (!$land) {
            return response()->json(['error' => 'Land not found'], 404);
        }
        $land_id = $land->id;

        $latestAction = Iot::where('land_id', $land_id)
            ->orderBy('created_at', 'desc')
            ->first();
        if (!$latestAction) {
            return response()->json(['error' => 'No action found'], 404);
        }
        $action = [
            'id' => $latestAction->id,
            'action_type' => $latestAction->action_type,
            'date' => $latestAction->created_at->format('Y-m-d'),
            'time' => $latestAction->created_at->format('H:i A')
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
            return response()->json([]);
        }
        $npk = json_decode($latest_fertilization->data);
        $water_level = json_decode($latest_irrigation->data);
        return response()->json([
            'water_level' => $water_level->water_level, 'npk' => $npk
        ]);
    }

    //return list of fertilization only or irrigation actions
    public function actionsByType($action_type)
    {
        $land = Auth::guard('sanctum')->user()->landowner->lands->first();
        if (!$land) {
            return response()->json(['error' => 'Land not found'], 404);
        }
        $land_id = $land->id;
        //        dd($land_id,$action_type);
        $iotActions = Iot::where('land_id', $land_id)
            ->where('action_type', $action_type)
            ->orderBy('created_at', 'desc')
            ->get();
        $actions = [];
        foreach ($iotActions as $action) {
            $actions[] = [
                'id' => $action->id,
                'date' => $action->created_at->format('Y-m-d'),
                'time' => $action->created_at->format('H:i A')
            ];
        }
        return response()->json(['actions' => $actions], 200);
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
        //dd($farmer);

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
