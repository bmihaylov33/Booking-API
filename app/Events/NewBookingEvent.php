<?php

namespace App\Events;

class NewBookingEvent
{
    public $booking;

    public function __construct($booking)
    {
        $this->booking = $booking;
    }
}
