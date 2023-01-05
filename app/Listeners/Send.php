<?php

namespace App\Listeners;

use App\Events\Activation;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Mail\Activation as SendAct;
use Mail; 

class Send
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
     * @param  Activation  $event
     * @return void
     */
    public function handle(Activation $event)
    {
        Mail::to($event->user->email)->send(new SendAct($event->user));
    }
}
