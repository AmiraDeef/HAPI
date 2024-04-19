<?php /** @noinspection PhpUndefinedFieldInspection */

namespace App\Http\Controllers\Crop;

use App\Http\Controllers\Controller;
use App\Http\Controllers\NotificationController;
use App\Http\Requests\ImageRequest;
use App\Models\Crop;
use App\Models\Detection;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use GuzzleHttp\Client;

class DetectionController extends Controller
{
    protected NotificationController $notificationController;
    public function __construct(NotificationController $notificationController)
    {
        $this->notificationController = $notificationController;
    }

    public function detect(ImageRequest $request): JsonResponse
    {
        try {
            $this->validateImage($request);
            $validatedData = $request->validated();
            // Validate crop
            $validatedCrop = $validatedData['crop'];
            $crop = Crop::findOrCreate(['name' => $validatedCrop]);
            $crop_id = $crop->id;

            $image = $request->file('image');

            $client = new Client();

            // Make an HTTP request to the crop disease API
            $response = $client->request('POST', 'https://crop-disease-api-0fqx.onrender.com/', [
                'multipart' => [
                    [
                        'name'     => 'image',
                        'contents' => fopen($image->path(), 'r'), // Open the image file
                        'filename' => $image->getClientOriginalName()
                    ]
                ],
                'timeout' => 60
            ]);

            if ($response->getStatusCode() === 200) {
                $responseData = json_decode($response->getBody(), true); // Decode response as associative array
                $transformedResponse = $this->transformResponse($responseData);
                $this->processDetectionResult($responseData, $image, $crop_id);
                return response()->json($transformedResponse);
            } else {
                return response()->json([
                    'error' => "API request failed. Status code: " . $response->getStatusCode(),
                ], 500);
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

    public function processDetectionResult(array $result, UploadedFile $image,int $cropId): void{
        $user = Auth::guard('sanctum')->user();
        //dd($user);
        if($user){
            $landId = $this->retrieveUserLandId();
            $this->store($user->id, $result, $image, $cropId);
            $this->notificationController->createNewDetectionNotification($landId, $user->username);
        }
    }
    public function retrieveUserLandId(): ?int
    {
        $user = Auth::guard('sanctum')->user();
//        ($user);
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
    private function enhanceDetections($detections)
    {
        return $detections->map(function ($detection) {
            $detection->detection = json_decode($detection->detection);
            $img_name = basename($detection->image);
            $imageUrl = Storage::url("detections/{$img_name}");
            $detection->image_url = $imageUrl;
            $timestamp = strtotime($detection->detected_at);
            $date = date('d/m/Y', $timestamp);
            $time = date('H:i A', $timestamp);
            // Create an array with the required data
            $result = [
                'id' => $detection->id,
                'username' => $detection->user->username,
                'image_url' => $imageUrl,
                'date' => $date,
                'time' => $time
            ];
            return $result;
        });
    }


    public function history(): JsonResponse
    {
        $detection_history = Detection::where('land_id', $this->retrieveUserLandId())->orderBy('detected_at', 'desc')->get();
        if ($detection_history->isEmpty()) {
            return response()->json(['error' => 'No detection history found'], 404);
        }

        $enhancedHistory = $this->enhanceDetections($detection_history);
        return response()->json($enhancedHistory);
    }
    public function show($id){
        $detection = Detection::find($id);
        if (!$detection) {
            return response()->json(['error' => 'Detection not found'], 404);
        }

        $user = Auth::guard('sanctum')->user();
        if (!$user || ($user->id !== $detection->user_id && $detection->land_id !== $this->retrieveUserLandId())) {
            return response()->json(['error' => 'Unauthorized to view this detection'], 403);
        }

       // $enhancedDetection = $this->enhanceDetections(collect([$detection]))->first();
//        $cropName = $detection->crop->name;
//        $enhancedDetection['crop'] = $cropName;
//        $transformedDetection = $this->transformResponse($detection->detection);
//        $enhancedDetection['detection'] = $transformedDetection;

//        return response()->json($enhancedDetection);
        $cropName = $detection->crop->name;
        $enhancedDetection['crop'] = $cropName;
        $transformedDetection = $this->transformResponse($detection->detection);
        $enhancedDetection['detection'] = $transformedDetection;

        return response()->json($enhancedDetection);
    }
    //fun return last detection
    public function lastOneDetection(): JsonResponse{

        $detections = Detection::where('land_id', $this->retrieveUserLandId())->latest('detected_at')->limit(1)->get();
        $enhancedDetections = $this->enhanceDetections($detections);

        if ($enhancedDetections->isEmpty()) {
            return response()->json(['error' => 'Detection not found'], 404);
        }

        return response()->json($enhancedDetections->first());
    }
    //return last 5 detection
    public function lastFiveDetection(): JsonResponse{

        $detections=Detection::where('land_id',$this->retrieveUserLandId())->latest('detected_at')->limit(5)->get();
        if($detections->isEmpty()){
            return response()->json(['error' => 'No detection found'], 404);
        }
        $enhancedHistory = $this->enhanceDetections($detections);
        return response()->json($enhancedHistory);
    }

// modifying the response to match with mobile ui

    public function transformResponse($responseData)
    {
        $responseContent = [];
        // Check if $responseData is an object
        if (is_object($responseData)) {
            $responseData = (array) $responseData; // Convert object to array
        }
        // Check if the plant health indicates the crop is healthy
        if ($responseData['plant_health'] === 'Corn___Healthy') {
            $responseContent = [
                "isHealthy" => true,
                "confidence" => isset($responseData['confidence']) ? $responseData['confidence'] : null,
                "diseases" => [],
            ];
        } else {
            // The crop has diseases
            $responseContent = [
                "isHealthy" => false,
                "confidence" => isset($responseData['confidence']) ? $responseData['confidence'] : null,
                "diseases" => [
                    [
                        "name" => $this->convertPlantHealthToDiseaseName($responseData['plant_health']),
                        "confidence" => isset($responseData['confidence']) ? $responseData['confidence'] : null,
                        "infoLink" => $this->generateInfoLink($responseData['plant_health']),
                    ]
                ],
            ];
        }
        return $responseContent;
    }


// Function to convert plant health to disease name
    private function convertPlantHealthToDiseaseName($plantHealth)
    {  $cleanedPlantHealth = str_replace("Corn___", "", $plantHealth);
        return str_replace("_", " ", $cleanedPlantHealth);
    }

// Function to generate info link based on disease name
    private function generateInfoLink($plantHealth)
    {
        $cleanedPlantHealth = str_replace("Corn___", "", $plantHealth);
          return "https://en.wikipedia.org/wiki/".
            str_replace("_", "%20", $cleanedPlantHealth);

    }

    //reset the detection history
//    public function resetDetectionHistory(): JsonResponse
//    {
//        $detections = Detection::where('land_id', $this->retrieveUserLandId())->get();
//        if ($detections->isEmpty()) {
//            return response()->json(['error' => 'No detection history found'], 404);
//        }
//
//        foreach ($detections as $detection) {
//            $detection->delete();
//        }
//
//        return response()->json(['message' => 'Detection history reset successfully'], 200);
//    }


}
