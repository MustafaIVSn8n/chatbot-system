<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Models\Chat;
use App\Mail\ChatReminderEmail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendChatReminder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $chat;

    /**
     * Create a new job instance.
     */
    public function __construct(Chat $chat)
    {
        $this->chat = $chat;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $agent = $this->chat->assignedAgent;

        if ($agent) {
            Log::info('Sending chat reminder email to agent: ' . $agent->email);
            Mail::to($agent->email)->send(new ChatReminderEmail($this->chat));
            Log::info('Chat reminder email sent to agent: ' . $agent->email);
        } else {
            Log::warning('No agent assigned to chat: ' . $this->chat->id);
        }
    }
}
