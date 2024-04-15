<?php /** @noinspection PhpUndefinedFieldInspection */

namespace App\Http\Controllers\Crop;

use App\Http\Controllers\Controller;
use App\Http\Controllers\NotificationController;
use App\Http\Requests\ImageRequest;
use App\Models\Crop;
use App\Models\Detection;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\File\UploadedFile;


class DetectionController extends Controller
{
    protected NotificationController $notificationController;
    public function __construct(NotificationController $notificationController)
    {
        $this->notificationController = $notificationController;
    }

    public function detect(ImageRequest $request): JsonResponse{
            try {
                $this->validateImage($request);
                $validatedData = $request->validated();
                //validate crop
                $validatedCrop = $validatedData['crop'];
                $crop = Crop::findOrCreate(['name' => $validatedCrop]);
                $crop_id= $crop->id;
                //$this->validateImage($validatedData['image']);
                $response = $this->sendImgToAI($request->file('image'));
//                if ($response->successful()) {
                if ($response) {
                    $this->processDetectionResult($response, $request->file('image'), $crop_id);
                    return response()->json($response);
                } else {
                    return response()->json(['error' => 'Failed to process image.'], 500);
              }
            } catch (RequestException $e) {
            return response()->json(['error' => 'Failed to connect to AI service.'], 500);
          }
    }
    private function validateImage(ImageRequest $request)
    {
        $validatedData = $request->validated();

        if (!$request->hasFile('image')) {
            return response()->json(['error' => 'Image is required.'], 400);
        }
        return null;
    }

    private function sendImgToAI(UploadedFile $image)
    {
        $imageStream = fopen($image->getRealPath(), 'rb');// Open the file in binary mode

        //$response = Http::attach('image', $image)->post("http://127.0.0.1:5000/detect");//url ai
        $responseContent = [
            "isHealthy" => false,
            "confidence" => 0.75,
            "diseases" => [
                [
                    "name" => "Corn Rust",
                    "confidence" => 0.8,
                    "infoLink" => "https://en.wikipedia.org/wiki/Corn_rust",
                ],
                [
                    "name" => "Leaf Blight",
                    "confidence" => 0.65,
                    "infoLink" => "https://en.wikipedia.org/wiki/Leaf_blight",
                ],
            ],
            //'Image' => Storage::url("detections/{basename($detection->image)}"),
        ];
        fclose($imageStream); // Close the file stream
        return $responseContent;
    }
    public function processDetectionResult(array $result, UploadedFile $image,int $cropId): void{
        $user = Auth::guard('api')->user();
        if($user){
            $landId = $this->retrieveUserLandId();
            $this->store($user->id, $result, $image,$cropId);
            $this->notificationController->createNewDetectionNotification($landId, $user->username);
        }
    }
    public function retrieveUserLandId(): ?int
    {
        $user = Auth::guard('api')->user();
        //dd($user);
        if (!$user) {
            return null;
        }else{
            if ($user->role === 'landowner') {
                return $user->landowner->lands()->first()->id;
            } else{
                return $user->farmer->land_id;
            }
        }
    }

    public function store($user_id,$result,$image,$crop_id){
//        $path = Storage::putFile('public/detections', $image);
        $path = $image->storeAs('detections', $image->getClientOriginalName(),'public');
        $detection = new Detection();
        $detection->user_id = $user_id;
        $detection->land_id = $this->retrieveUserLandId();
        $detection->image = $path;
        $detection->detection = json_encode($result);
        $detection->crop_id = $crop_id;
        $detection->detected_at = now();
        $detection->save();
    }

    public function history(): JsonResponse
    {
        //dd($this->retrieveUserLandId());
         $detection_history=Detection::where('land_id',$this->retrieveUserLandId())->orderBy('detected_at', 'desc')->get();
         //dd($detection_history);
        if($detection_history->isEmpty()) {
            return response()->json(['error' => 'No detection history found'], 404);
        }

        $enhancedHistory = $detection_history->map(function ($detection) {
            $detection->detection = json_decode($detection->detection);
            $img_name = basename($detection->image);
            $imageUrl = Storage::url("detections/{$img_name}"); // Use Storage facade
            $detection->image_url = $imageUrl;
            $timestamp = strtotime($detection->detected_at);
            $date = date('d/m/Y', $timestamp);
            $time = date('H:i:s', $timestamp);
            // Create an array with the required data
            $result = [
                'username' => $detection->user->username, // Assuming you want the currently logged-in user's username
                'image_url' => $imageUrl,
                'detection' => $detection->detection,
                'date' => $date,
                'time' => $time
            ];
            return $result;
        });
         return response()->json($enhancedHistory);
    }


}
