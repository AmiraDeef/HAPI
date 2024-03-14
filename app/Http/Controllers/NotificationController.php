<?php

namespace App\Http\Controllers;

use App\Models\Land;
use App\Models\Notification;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index(): JsonResponse
    {
        $user = auth()->user();
        if ($user->role === 'landowner') {
            $notifications = Notification::where('user_id', $user->id)
                ->orderBy('status', 'asc')
                ->latest()
                ->get();
        } elseif ($user->role ==='farmer'&& $user->farmer) {
           // dd($user->farmer->land->unique_land_id);
            $land_id = $user->farmer->land->id;
                $notifications = Notification::whereIn('type', ['new_detection', 'new_iot_actions', 'message'])
                    ->where('land_id', $land_id)
                    ->orderBy('status', 'asc')
                    ->latest()
                    ->get();
          // dd($notifications);
        } else {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
//        dd($notifications);
        return response()->json(['notifications' => $notifications], 200);

    }


    public function createNewFarmerNotification($land_id, $username)
    {
        try {
            // Attempt to find the land with the provided ID
            $land = Land::where('id', $land_id)->with('landowner')->firstOrFail();
            $landowner = $land->landowner;

            if ($landowner) {
                Notification::create([
                    'land_id' => $land_id,
                    'user_id' => $landowner->user->id,
                    'message' => $username . ' has requested to access your land',
                    'type' => 'new_farmer',
                    'status' => 'unread',
                ]);

            }

        } catch (ModelNotFoundException $e) {
            // Handle case where land is not found
            return response()->json(['error' => 'Land with ID ' . $land_id . ' not found'], 404);
        }
    }

    public function createNewDetectionNotification($land_id,$username){
        if(!$land_id){
            return response()->json(['error' => 'Unauthorized to create notification'], 403);
        }
        $land = Land::with(['landowner', 'farmers'])->where('unique_land_id', $land_id)->first();
        if (!$land) {
            return response()->json(['error' => 'Land not found'], 404);
        }
        $landowners = $land->landowner->user;
        $farmers = $land->farmers->pluck('user');
        $users = Collection::wrap($landowners)->merge($farmers);
        foreach ($users as $user) {
            Notification::create([
                'land_id' => $land_id,
                'user_id' => $user->id,
                'message' => $username . ' has recorded a new detection',
                'type' => 'new_detection',
                'status' => 'unread',
            ]);
        }

    }
    public function createNewIotNotification($land_id){
        $user = Auth::user();
        //dd($user->land->unique_land_id);
        if ($user->landowner){
            $land_id = $user->landowner->lands->first()->unique_land_id;
        } elseif($user->farmer){
            $land_id = $user->land->unique_land_id;
        } else {
            return response()->json(['error' => 'Unauthorized to create notification'], 403);
        }
        $land = Land::with(['landowner', 'farmers'])->where('unique_land_id', $land_id)->first();

        if (!$land) {
            return response()->json(['error' => 'Land not found'], 404);
        }

        $landowners = $land->landowner->user;
        $farmers = $land->farmers->pluck('user');
        $users = Collection::wrap($landowners)->merge($farmers);
        foreach ($users as $user) {
            Notification::create([
                'land_id' => $land_id,
                'user_id' => $user->id,
                'message' =>'New IoT actions have been recorded',//I'll add the iot data later
                'type' => 'new_iot_actions',
                'status' => 'unread',
            ]);
        }
    }
    public function createMessageNotification(Request $request){
       $validated_msg = $request->validate([
           'message' => 'required|string'
         ]);
        $message = $validated_msg['message'];

        $user = Auth::user();
        //dd($user->land->unique_land_id);
        if ($user->landowner){
            $land_id = $user->landowner->lands->first()->unique_land_id;
        } elseif($user->farmer){
            $land_id = $user->land->unique_land_id;
        } else {
            return response()->json(['error' => 'Unauthorized to create notification'], 403);
        }
        $land = Land::with(['landowner', 'farmers'])->where('unique_land_id', $land_id)->first();

        if (!$land) {
            return response()->json(['error' => 'Land not found'], 404);
        }

        $landowners = $land->landowner->user;
        $farmers = $land->farmers->pluck('user');
        $users = Collection::wrap($landowners)->merge($farmers);

        foreach ($users as $user) {
            Notification::create([
                'land_id' => $land->id,
                'user_id' => $user->id,
                'message' => $message,
                'type' => 'message',
                'status' => 'unread',
            ]);
        }
        return response()->json(['message' => 'Message sent successfully'], 201);
    }

    public function seenNotification(Request $request,$id): JsonResponse
    {
        $notification = Notification::findOrFail($id);
        $notification->update(['status' => 'read']);

        return response()->json(['message' => 'Notification marked as read.']);

    }
}
