<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use http\Env\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
class ProfileController extends Controller
{
    /** @noinspection PhpUndefinedFieldInspection */
    public function changeCrop(Request $request): JsonResponse
    {
        $landowner=Auth::user()->landowner;

        if (!Hash::check($request->input('password'), Auth::user()->password)) {
            return response()->json(['error' => 'Incorrect password'], 401);
        }
        //rest previous history

        // crop selection but this after build crop controller ")
        return response()->json(['message' => 'crop changed succsessfuly']);
    }
    public function deleteAccount(Request $request): JsonResponse
    {
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

}
