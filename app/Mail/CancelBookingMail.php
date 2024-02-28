<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class CancelBookingMail extends Mailable
{
    public $booking;

    public function __construct($booking)
    {
        $this->booking = $booking;
    }

    public function build()
    {
        return $this->view('emails.cancel_booking')
            ->subject('Cancelation Booking Alert');
    }
}
