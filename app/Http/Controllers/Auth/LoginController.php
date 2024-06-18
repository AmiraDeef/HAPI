<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\Crop;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use function response;

class LoginController extends Controller
{

    public function login(LoginRequest $request): JsonResponse
    {

        $validatedData = $request->validated();
        if (!$validatedData) {
            return response()->json(['error' => $request->validator->errors()->messages()], 422);
        }


        $credentials = $request->only('phone_number', 'password');
        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $token = $user->createToken('authToken')->plainTextToken;
            $responseData = [
                'token' => $token,
                'username' => $user->username,
                'role' => $user->role,
                'land_id' => null,
            ];

            if ($user->role === 'landowner' && $user->landowner) {
                $responseData['land_id'] = $user->landowner->lands->first()->unique_land_id;
                $crop = Crop::find($user->landowner->lands->first()->crop_id);
                if (!$crop) {
                    $responseData['crop'] = null;
                    return response()->json($responseData);
                }
                $responseData['crop'] = $crop->name;
            } elseif ($user->role === 'farmer' && $user->farmer) {
                $responseData['land_id'] = $user->farmer->land_id;
            }

            return response()->json($responseData);
        }

        return response()->json(['message' => 'Invalid Number or password'], 401);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(null);
    }
}
