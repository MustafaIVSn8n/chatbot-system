<?php

namespace App\Console;

use App\Jobs\SendChatReminder;
use App\Models\Chat;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->call(function () {
            Log::info('Running scheduler...');
            $chats = Chat::where('status', 'open')
                        ->where('last_activity_at', '<', now()->subMinutes(10))
                        ->get();

            Log::info('Found ' . $chats->count() . ' chats to remind.');

            foreach ($chats as $chat) {
                Log::info('Dispatching SendChatReminder job for chat: ' . $chat->id);
                SendChatReminder::dispatch($chat);
            }
        })->everyTenMinutes();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
