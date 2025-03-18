<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class AdminWelcomeEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $password;
    public $websites;

    public function __construct(User $user, string $password, Collection $websites)
    {
        $this->user = $user;
        $this->password = $password;
        $this->websites = $websites;
    }

    public function build()
    {
        $adminPanelLink = url('/login'); // Or the actual URL of your admin panel

        return $this->subject('Welcome to the Admin Panel')
                    ->view('emails.admin_welcome', [
                        'user' => $this->user,
                        'password' => $this->password,
                        'adminPanelLink' => $adminPanelLink,
                        'websites' => $this->websites,
                    ]);
    }
}