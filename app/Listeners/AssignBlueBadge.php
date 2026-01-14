<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Verified;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class AssignBlueBadge
{
    /**
     * Handle the event.
     */
    public function handle(Verified $event): void
    {
        // automatically set is_verified to true when email is verified
        $user = $event->user;
        $user->is_verified = true;
        $user->save();
    }
}