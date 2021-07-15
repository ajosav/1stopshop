<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AppointmentConfirmation extends Notification
{
    use Queueable;

    public $request, $mechanic;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($request, $mechanic)
    {
        $this->request = $request;
        $this->mechanic = $mechanic;
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
        $first_name = $notifiable->first_name;
        $mechanic_address = $this->mechanic->office_address;
        return (new MailMessage)
                ->subject('Appointment Confirmation')
                ->view('email/appointment_confirmation', ['request' => $this->request, 'first_name' => $first_name, 'mechanic_address' => $mechanic_address]);
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
