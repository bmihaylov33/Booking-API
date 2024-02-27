<?php

namespace App\Listeners;

use App\Mail\CancelBookingMail;
use Illuminate\Support\Facades\Mail;

class CancelBookingListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(object $event): void
    {
        Mail::to('hotel@example.com')->send(new CancelBookingMail($event->booking));
    }
}
