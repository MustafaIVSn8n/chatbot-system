<?php

namespace App\Listeners;

use App\Events\AdminUserCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;
use App\Mail\AdminWelcomeEmail;

class SendWelcomeEmail implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(AdminUserCreated $event)
    {
        if ($event->isNewUser) {
            Mail::to($event->user->email)->send(new AdminWelcomeEmail($event->user, $event->password, $event->websites));
        }
    }
}