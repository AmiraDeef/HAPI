<?php

namespace App\Jobs;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * @property String $landId
 */
class SendLandToIot implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $landId;

    public function __construct($landId)
    {
        $this->landId = $landId;
    }

    public function handle(): void
    {
        try {
//            $this->landId=Land::latest();
//            $response = Http::post(route('land.id', ['land_id' => $this->landId]));


//            if ($response->getStatusCode() !== 200) {
//                Log::error("Failed to send land ID to IoT device (land ID: {$this->landId}). Status code: {$response->getStatusCode()}");
//            }
        } catch (Exception $e) {
            Log::error("Error sending land ID to IoT device (land ID: {$this->landId}): {$e->getMessage()}");
        }
    }
}
