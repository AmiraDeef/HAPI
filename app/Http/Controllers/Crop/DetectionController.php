<?php

namespace App\Http\Controllers\Crop;

use App\Http\Controllers\Controller;
use App\Http\Requests\ImageRequest;
use App\Models\Detection;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;


class DetectionController extends Controller
{

    public function detectImageForGuest(ImageRequest $request): JsonResponse{
            try {
                if (!$request->hasFile('image')) {
                    return response()->json(['error' => 'Image is required.'], 400);
                }

                $image = fopen($request->file('image'), 'rb'); // Open the file in binary mode

                $response = Http::attach('image', $image)->post("http://127.0.0.1:5000/detect");//url ai

                fclose($image); // Close the file handle

                if ($response->successful()) {
                $result = $response->json();
                return response()->json($result);
              } else {
                return response()->json(['error' => 'Failed to process image.'], $response->status());
              }
            } catch (RequestException $e) {
            return response()->json(['error' => 'Failed to connect to AI service.'], 500);
          }
    }



       //detection if uesr auth


//    public function detectImage(ImageRequest $request): JsonResponse
//    {
//        try {
//            if (!$request->hasFile('image')) {
//                return response()->json(['error' => 'Image is required.'], 400);
//            }
//
//            $image = $request->file('image');
//
//            $response = Http::attach('image', $image)->post("http://127.0.0.1:5000/detect");//url ai
//
//
//            if ($response->successful()) {
//                $result = $response->json();
//                if(Auth::check()){
//                    $this->saveDetectionResult(Auth::id(), $result,$image);
//                }
//                return response()->json($result);
//            } else {
//                return response()->json(['error' => 'Failed to process image.'], $response->status());
//            }
//        } catch (RequestException $e) {
//            return response()->json(['error' => 'Failed to connect to AI service.'], 500);
//        }
//    }

    public function history(): JsonResponse
    {
//        // history of user->farmer detection
//        $detection_history = Detection::where('user_id', Auth::id())->get();
//
//
//        // history of user->landowner but specific land detection
//         $detection_history=Detection::where('farm_id',Auth::user()->landowner->farm_id)->get();
//
//

        return response()->json();

    }

    //if result in json ,I should encode it first

    public function store($user_id,$result,$image){
        $detection = new Detection();
        $detection->user_id = $user_id;
        $detection->image = $image;
        $detection->detection = json_encode($result);
        $detection->detected_at = now();
        $detection->save();

    }
}
