<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ApointmentCancellation extends Notification
{
    use Queueable;

    public $request, $user, $appointment;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($request, $user, $appointment)
    {
        $this->request     = $request;
        $this->request     = $user;
        $this->appointment = $appointment;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $user_firstname = $this->user->first_name;
        $user_lastname  = $this->user->last_name;
        $phone_number   = $this->user->phone_number;
        return (new MailMessage)
                ->subject('Appointment Cancellation')
                ->view('email/appointment_cancellation', [
                    'user_firstname'    => $user_firstname,
                    'first_name'        => $notifiable->first_name,
                    'user_lastname'     => $user_lastname,
                    'phone_number'      => $phone_number,
                    'appointment'       => $this->appointment
                ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
