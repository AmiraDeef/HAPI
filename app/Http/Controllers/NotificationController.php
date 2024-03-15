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

/**
 * Class NotificationController
 * @package App\Http\Controllers
 *
 * This class handles all notification-related actions.
 */
class NotificationController extends Controller
{
    /**
     * Fetches and returns all notifications for the currently authenticated user.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        /** @var User $user */
        $user = auth()->user();
        if ($user->role === 'landowner') {
            $notifications = Notification::where('user_id', $user->id)
                ->orderBy('status', 'asc')
                ->latest()
                ->get();
        } elseif ($user->role ==='farmer'&& $user->farmer) {
            $land_id = $user->farmer->land->id;
            $notifications = Notification::whereIn('type', ['new_detection', 'new_iot_actions', 'message'])
                ->where('land_id', $land_id)
                ->orderBy('status', 'asc')
                ->latest()
                ->get();
        } else {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        return response()->json(['notifications' => $notifications]);
    }

    /**
     * Creates a new farmer notification.
     *
     * @param $land_id
     * @param $username
     * @return JsonResponse
     */
    public function createNewFarmerNotification($land_id, $username): JsonResponse
    {
        try {
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
            return response()->json(['error' => 'Land with ID ' . $land_id . ' not found'], 404);
        }
    }

    /**
     * Creates a new detection notification.
     *
     * @param $land_id
     * @param $username
     * @return JsonResponse
     */
    public function createNewDetectionNotification($land_id,$username)
    {
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

    /**
     * Creates a new IoT notification.
     *
     * @param $land_id
     * @return JsonResponse
     */
    public function createNewIotNotification($land_id)
    {
        /** @var User $user */
        $user = Auth::user();
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
                'message' =>'New IoT actions have been recorded',
                'type' => 'new_iot_actions',
                'status' => 'unread',
            ]);
        }
    }

    /**
     * Creates a new message notification.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function createMessageNotification(Request $request)
    {
        $validated_msg = $request->validate([
            'message' => 'required|string'
        ]);
        $message = $validated_msg['message'];
        /** @var User $user */
        $user = Auth::user();
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

    /**
     * Marks a notification as read.
     *
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function seenNotification(Request $request,$id): JsonResponse
    {
        $notification = Notification::findOrFail($id);
        $notification->update(['status' => 'read']);

        return response()->json(['message' => 'Notification marked as read.']);
    }
}
