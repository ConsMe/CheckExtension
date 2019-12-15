<?php

namespace App\Listeners;

use App\Events\NewMessageForChecker;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendMessageToChecker
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
     * @param  NewMessageForChecker  $event
     * @return void
     */
    public function handle(NewMessageForChecker $event)
    {
        //
    }
}
