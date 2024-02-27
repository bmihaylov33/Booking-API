<?php

namespace App\Events;

class CancelBookingEvent
{
     public $booking;

    public function __construct($booking)
    {
        $this->booking = $booking;
    }
}
