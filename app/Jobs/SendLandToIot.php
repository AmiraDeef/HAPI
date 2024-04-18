<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

/**
 * @property String $landId
 */
class SendLandToIot implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The land ID to be sent to the IoT device.
     *
     * @var string
     */
      protected string $landId;
    /**
     * Create a new job instance.
     * *
     * @param string $landId
     */

    public function __construct(String $landId)
    {
        $this->landId = $landId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $url="https://221f-154-237-193-245.ngrok-free.app/mock-iot";
        try {
            $response = Http::post($url, [
                'json' => [
                    'land_id' => $this->landId,
                ],
            ]);

            if ($response->getStatusCode() !== 200) {
                Log::error("Failed to send land ID to IoT device (land ID: {$this->landId}). Status code: {$response->getStatusCode()}");
            }
        } catch (Exception $e) {
            Log::error("Error sending land ID to IoT device (land ID: {$this->landId}): {$e->getMessage()}");
        }
    }
}
