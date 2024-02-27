<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class NewBookingMail extends Mailable
{
    public $booking;

    public function __construct($booking)
    {
        $this->booking = $booking;
    }

    public function build()
    {
        return $this->view('emails.new_booking')
            ->subject('New Booking Alert');
    }
}
