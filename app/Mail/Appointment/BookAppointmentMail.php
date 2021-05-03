<?php

namespace App\Mail\Appointment;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BookAppointmentMail extends Mailable
{
    public $sender, $receiver, $appointment;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($sender, $receiver, $appointment)
    {
        $this->sender = $sender;
        $this->receiver = $receiver;
        $this->appointment = $appointment;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from($this->sender->email, $this->sender->first_name . ' ' . $this->sender->last_name)
            ->markdown('email/book_appointment');
    }
}
