<?php

namespace App\Listeners;

use App\Events\AdminUserCreated;
use App\Events\AdminUserUpdated;
use App\Events\AdminUserDeleted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;
use App\Mail\SuperAdminNotification;

class NotifySuperAdmin implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(AdminUserCreated $event)
    {
        $superAdminEmail = config('mail.super_admin_email'); // Configure in config/mail.php
        Mail::to($superAdminEmail)->send(new SuperAdminNotification($event->user, 'created'));
    }

    public function handleAdminUserUpdated(AdminUserUpdated $event)
    {
        $superAdminEmail = config('mail.super_admin_email'); // Configure in config/mail.php
        Mail::to($superAdminEmail)->send(new SuperAdminNotification($event->user, 'updated'));
    }

    public function handleAdminUserDeleted(AdminUserDeleted $event)
    {
        $superAdminEmail = config('mail.super_admin_email'); // Configure in config/mail.php
        Mail::to($superAdminEmail)->send(new SuperAdminNotification($event->user, 'deleted'));
    }
}