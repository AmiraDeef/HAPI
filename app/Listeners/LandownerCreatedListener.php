<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\Farm;
use Illuminate\Support\Str;
use App\Models\User;



class LandownerCreatedListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
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
        $landowner->farm()->create([
           'unique_farm_id'=> Str::random(8)

       ]);
    }
}
