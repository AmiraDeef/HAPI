<?php /** @noinspection ALL */

namespace App\Http\Controllers;

use App\Models\Land;
use App\Models\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\User;
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
        } elseif ($user->role === 'farmer') {
            $notifications = Notification::whereIn('type', ['new_farmer', 'new_detection'])
                ->where('land_id', auth()->land->unique_land_id)
                ->orderBy('status', 'asc')
                ->latest()
                ->get();
        } else {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return response()->json(['notifications' => $notifications], 200);

    }


    public function createNewFarmerNotification($land_id, $username)
    {
        try {
            // Attempt to find the land with the provided ID
            $land = Land::where('unique_land_id', $land_id)->with('landowner')->firstOrFail();
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

            // Success message (optional)
            // return response()->json(['message' => 'Notifications created successfully']);

        } catch (ModelNotFoundException $e) {
            // Handle case where land is not found
            return response()->json(['error' => 'Land with ID ' . $land_id . ' not found'], 404);
        }
    }

    public function createNewDetectionNotification($land_id,$username){
        $land = Land::findOrFail($land_id);
        $landowner=$land->landowner;
        $farmers=$land->farmers;
        $users = $landowner->merge($farmers)->pluck('user');
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
        $land = Land::findOrFail($land_id);
        $landowner=$land->landowner;
        $farmers=$land->farmers;
        $users = $landowner->merge($farmers)->pluck('user');
        foreach ($users as $user) {
            Notification::create([
                'land_id' => $land_id,
                'user_id' => $landowner->user->id,
                'message' =>'New IoT actions have been recorded',//I'll add the iot data later
                'type' => 'new_iot_actions',
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
}
