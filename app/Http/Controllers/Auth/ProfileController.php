<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    /** @noinspection PhpUndefinedFieldInspection */
    public function check_password(Request $request)
    {
        $landowner = Auth::user()->landowner;

        if (!Hash::check($request->input('password'), Auth::user()->password)) {
            return response()->json(['error' => 'Incorrect password'], 401);
        }
        return response()->json([]);
    }

    public function deleteAccount(Request $request): JsonResponse
    {
        if (!Hash::check($request->input('password'), Auth::user()->password)) {
            return response()->json(['error' => 'Incorrect password'], 401);
        }
        $request->user()->delete();
        return response()->json(['message' => 'Account deleted successfully']);
    }
    //there is no change password yet
    public function changePassword(Request $request): JsonResponse
    {
        $user = Auth::user();
        $user->update(['password' => bcrypt($request->new_password)]);
        return response()->json(['message' => 'Password changed successfully']);
    }
    //list of farmer for specific land
    public function listFarmers(Request $request): JsonResponse
    {
        $landowner = Auth::user()->landowner;
        $land = $landowner->lands()->first();
        $farmers = $land->farmers;
        //list farmers name
        $farmers_names = $farmers->map(function ($farmer) {
            return $farmer->user->username;
        });

        return response()->json($farmers_names);
    }


}
