<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use App\Events\AdminUserCreated;
use App\Events\AdminUserUpdated;
use App\Events\AdminUserDeleted;
use App\Listeners\SendWelcomeEmail;
use App\Listeners\NotifySuperAdmin;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        AdminUserCreated::class => [
            SendWelcomeEmail::class,
            NotifySuperAdmin::class,
        ],
        AdminUserUpdated::class => [
        ],
        AdminUserDeleted::class => [
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        parent::boot();

        Event::listen(
            AdminUserUpdated::class,
            [NotifySuperAdmin::class, 'handleAdminUserUpdated']
        );

        Event::listen(
            AdminUserDeleted::class,
            [NotifySuperAdmin::class, 'handleAdminUserDeleted']
        );
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
