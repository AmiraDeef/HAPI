<?php

namespace App\Http\Controllers\Land;

use App\Http\Controllers\Controller;
use App\Models\Iot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class LandHistoryController extends Controller
{
    public function history(Request $request)
    {
        $land=Auth::guard('sanctum')->user()->landowner->lands->first();
        if(!$land){
            return response()->json(['error' => 'Land not found'], 404);
        }
        $land_id=$land->unique_land_id;

        $iotActions= Iot::where('land_id', $land_id)
            ->orderBy('created_at', 'desc')
            ->get();
        $history = [];
        foreach ($iotActions as $action) {
            $history[] = [
                'id' => $action->id,
                'data' => json_decode($action->data), // if needed
                'action_type' => $action->action_type,
                'date' => $action->created_at->format('Y-m-d'),
                'time' => $action->created_at->format('H:i A')
            ];
        }
        return response()->json(['history' => $history], 200);
    }
    public function show($id)
    {
        $land=Auth::guard('sanctum')->user()->landowner->lands->first();
        if(!$land){
            return response()->json(['error' => 'Land not found'], 404);
        }
        $land_id=$land->unique_land_id;

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
        $land=Auth::guard('sanctum')->user()->landowner->lands->first();
        if(!$land){
            return response()->json(['error' => 'Land not found'], 404);
        }
        $land_id=$land->unique_land_id;

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

    public function latestNPK()
    {
        $land=Auth::guard('sanctum')->user()->landowner->lands->first();
        if(!$land){
            return response()->json(['error' => 'Land not found'], 404);
        }
        $land_id=$land->unique_land_id;

        $latestAction = Iot::where('land_id', $land_id)
            ->where('action_type', 'fertilization')
            ->orderBy('created_at', 'desc')
            ->first();
        if (!$latestAction) {
            return response()->json(['error' => 'No fertilization action found'], 404);
        }
        $npk = json_decode($latestAction->data);
        return response()->json(['npk' => $npk], 200);
    }
    //return list of fertilization only or irrigation actions
    public function actionType($action_type)
    {
        $land=Auth::guard('sanctum')->user()->landowner->lands->first();
        if(!$land){
            return response()->json(['error' => 'Land not found'], 404);
        }
        $land_id=$land->unique_land_id;

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
    //reset land history
//    public function reset()
//    {
//        $land=Auth::guard('sanctum')->user()->landowner->lands->first();
//        if(!$land){
//            return response()->json(['error' => 'Land not found'], 404);
//        }
//        $land_id=$land->unique_land_id;
//
//        Iot::where('land_id', $land_id)->delete();
//        return response()->json(['message' => 'Land history reset'], 200);
//    }



}
