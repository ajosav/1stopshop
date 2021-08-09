<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AppointmentConfirmation extends Notification
{
    use Queueable;

    public $request, $mechanic, $appointment;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($request, $mechanic, $appointment)
    {
        $this->request = $request;
        $this->mechanic = $mechanic;
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
        $first_name = $notifiable->first_name;
        $mechanic_address = $this->mechanic->office_address;
        $mechanic_shop_name = $this->mechanic->shop_name;
        $phone_number = $this->mechanic->phone_number;
        return (new MailMessage)
                ->subject('Appointment Confirmation')
                ->view('email/appointment_confirmation', [
                    'request' => $this->request, 
                    'first_name' => $first_name, 
                    'phone_number' => $phone_number, 
                    'mechanic_address' => $mechanic_address,
                    'mechanic_shop_name' => $mechanic_shop_name,
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
