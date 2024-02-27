<?php

namespace App\Providers;

use App\Events\CancelBookingEvent;
use App\Events\NewBookingEvent;
use App\Events\UserRegistered;
use App\Listeners\CancelBookingListener;
use App\Listeners\CreatePassportClient;
use App\Listeners\NewBookingListener;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

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
            CreatePassportClient::class,
        ],
        UserRegistered::class => [
            CreatePassportClient::class,
        ],
         NewBookingEvent::class => [
            NewBookingListener::class,
        ],
        CancelBookingEvent::class => [
            CancelBookingListener::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
