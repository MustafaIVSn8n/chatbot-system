<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class AdminUserCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
    public $isNewUser;
    public $password;
    public $websites;

    public function __construct(User $user, bool $isNewUser, string $password, Collection $websites)
    {
        $this->user = $user;
        $this->isNewUser = $isNewUser;
        $this->password = $password;
        $this->websites = $websites;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}