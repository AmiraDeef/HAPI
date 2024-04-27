<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Str;


class LandownerCreatedListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    protected $uniqueLandId;
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  Registered  $event
     * @return void
     */
    public function handle(Registered $event): void
    {
        $landowner = $event->user->landowner;
        if ($landowner->lands()->count() === 0) {
            // If no lands associated, create a new land with a unique ID
            $this->uniqueLandId = Str::random(8);
            $landowner->lands()->create([
                'unique_land_id' => $this->uniqueLandId
            ]);
        } // Add logic for multiple lands later
    }

    public function getUniqueLandId(): ?string
    {
        return $this->uniqueLandId;
    }
}
