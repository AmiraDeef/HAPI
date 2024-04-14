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
    protected $notificationController;
    public function __construct()
    {
        $this->notificationController = new NotificationController();
    }

    public function detect(ImageRequest $request): JsonResponse{
            try {
                $this->validateImage($request);
                $validatedData = $request->validated();
                //validate crop
                $validatedCrop = $validatedData['crop']; //remember to add to DB later
                //$this->validateImage($validatedData['image']);
                $response = $this->sendImgToAI($request->file('image'));
//                if ($response->successful()) {
                if ($response) {
                    $this->processDetectionResult($response, $request->file('image'), $validatedCrop);
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
    protected function processDetectionResult(array $result, UploadedFile $image,string $validatedCrop): void{
        $user = Auth::guard('api')->user();
        if($user){
            $land_id = $this->retrieveUserLandId();
            $this->store($user->id, $result, $image,$validatedCrop);

            $this->notificationController->createNewDetectionNotification($land_id, $user->username);
        }
    }
    protected function retrieveUserLandId(): ?int
    {
        $user = Auth::guard('api')->user();
        if ($user->landowner) {
            return $user->landowner->lands->first()->id;
        } elseif ($user->farmer) {
            return $user->farmer->land->id;
        } else {
            return null;
        }
    }

    public function store($user_id,$result,$image,$crop){
//        $path = Storage::putFile('public/detections', $image);
        $path = $image->storeAs('detections', $image->getClientOriginalName(),'public');
        $detection = new Detection();
        $detection->user_id = $user_id;
        $detection->land_id = $this->retrieveUserLandId();
        $detection->image = $path;
        $detection->detection = json_encode($result);
        //$detection->detection['crop'] = $crop;
        $detection->detected_at = now();
        $detection->save();
    }

    public function history(): JsonResponse
    {
         $detection_history=Detection::where('land_id',$this->retrieveUserLandId())->get();
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
