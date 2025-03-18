<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class SuperAdminNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $action;

    public function __construct(User $user, string $action)
    {
        $this->user = $user;
        $this->action = $action;
    }

    public function build()
    {
        return $this->subject('Admin User ' . ucfirst($this->action))
                    ->view('emails.superadmin_notification'); // Create resources/views/emails/superadmin_notification.blade.php
    }
}