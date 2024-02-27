<?php

namespace App\Listeners;

use App\Mail\NewBookingMail;
use Illuminate\Support\Facades\Mail;

class NewBookingListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    public function handle(NewBookingEvent $event)
    {
        Mail::to('hotel@example.com')->send(new NewBookingMail($event->booking));
    }
}
