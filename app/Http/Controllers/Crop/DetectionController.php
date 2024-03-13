<?php /** @noinspection PhpUndefinedFieldInspection */

namespace App\Http\Controllers\Crop;

use App\Http\Controllers\Controller;
use App\Http\Controllers\NotificationController;
use App\Http\Requests\ImageRequest;
use App\Models\Detection;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\File\UploadedFile;


class DetectionController extends Controller
{

    public function detect(ImageRequest $request): JsonResponse{
            try {
                $this->validateImage($request);
                $response = $this->sendImgToAI($request->file('image'));

                if ($response->successful()) {
                $result = $response->json();
                    if (Auth::check()) {
                        $this->store(Auth::id(), $result, $request->file('image'));
                        $detection_notify= new NotificationController();
                        $detection_notify->createNewDetectionNotification(Auth::user()->land->unique_land_id,Auth::user()->username);
                    }
                return response()->json($result);
              } else {
                return response()->json(['error' => 'Failed to process image.'], $response->status());
              }
            } catch (RequestException $e) {
            return response()->json(['error' => 'Failed to connect to AI service.'], 500);
          }
    }
    private function validateImage(ImageRequest $request)
    {
        $validatedData = $request->validated();

        if (!$validatedData) {
            return response()->json(['error' => $request->validator->errors()->messages()], 422);
        }

        if (!$request->hasFile('image')) {
            return response()->json(['error' => 'Image is required.'], 400);
        }
        return null;
    }

    private function sendImgToAI(UploadedFile $image): Response
    {
        $imageStream = fopen($image->getRealPath(), 'rb');// Open the file in binary mode

        $response = Http::attach('image', $image)->post("http://127.0.0.1:5000/detect");//url ai

         fclose($imageStream); // Close the file stream
        return $response;
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
//                    $this->store(Auth::id(), $result,$image);
//                }
//                return response()->json($result);
//            } else {
//                return response()->json(['error' => 'Failed to process image.'], $response->status());
//            }
//        } catch (RequestException $e) {
//            return response()->json(['error' => 'Failed to connect to AI service.'], 500);
//        }
//    }
//if result in json ,I should encode it first

    public function store($user_id,$result,$image){
        $path = Storage::putFile('detections', $image);
        $detection = new Detection();
        $detection->user_id = $user_id;
        $detection->image = $path;
        $detection->detection = json_encode($result);
        $detection->detected_at = now();
        $detection->save();

    }

    public function history(): JsonResponse
    {
//        // history of user->farmer detection
//        $detection_history = Detection::where('user_id', Auth::id())->get();
//
        // history of user->landowner but specific land detection
         $detection_history=Detection::where('land_id',Auth::user()->land_id)->get();
         return response()->json($detection_history, 200);

    }


}
