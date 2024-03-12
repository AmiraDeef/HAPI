<?php

namespace App\Http\Controllers;

use App\Models\Land;
use App\Models\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(): JsonResponse
    {
        $user=auth()->user();
        if ($user->role === 'landowner') {
            $notifications = Notification::where('user_id', $user->id)
                ->orderBy('status', 'asc')
                ->latest()
                ->get();
        } elseif ($user->role === 'farmer'){
            $notifications = Notification::whereIn('type', ['new_farmer', 'new_detection'])
                ->where('land_id', auth()->land->unique_land_id)
                ->orderBy('status', 'asc')
                ->latest()
                ->get();
        }
        else{
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return response()->json(['notifications' => $notifications], 200);

    }
    private function createNewFarmerNotification($land_id,$username){
        $landowners = Land::findOrFail($land_id)->landowners;
           foreach ($landowners as $landowner) {
               Notification::create([
                    'land_id' => $land_id,
                    'user_id' => $landowner->user->id,
                    'message' => $username . ' has requested to access your land',
                    'type' => 'new_farmer',
                    'status' => 'unread',
                ]);
           }

    }
    public function seenNotification(Request $request,$id): JsonResponse
    {
        $notification = Notification::findOrFail($id);
        $notification->update(['status' => 'read']);

        return response()->json(['message' => 'Notification marked as read.']);

    }



    public function store(Request $request){

        $validated_data = $request->validate([
            'type' => 'required|string',
            'message' => 'required|string',

        ]);
        $land = Land::find(auth()->land);
        if (!$land) {
            return response()->json(['error' => 'Land not found'], 404);
        }
        $notification = $land->notifications()->create([
            'user_id' => $land->landowner->user->id,
            'message' => $validated_data['message'],
        ]);
        return response()->json(['notification' => $notification], 201);
    }
}
