<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class AdminUserUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
    public $passwordChanged;

    public function __construct(User $user, bool $passwordChanged)
    {
        $this->user = $user;
        $this->passwordChanged = $passwordChanged;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}