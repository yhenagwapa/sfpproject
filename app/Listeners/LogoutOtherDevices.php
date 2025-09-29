<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;

class LogoutOtherDevices
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(object $event): void
    {
        // Delete all other sessions for this user
        DB::table('sessions')
            ->where('user_id', $event->user->id)
            ->where('id', '!=', session()->getId())
            ->delete();
    }
}
