<?php

namespace App\Services\Mail;

use App\Mail\SendEmailToRecipients;
use Illuminate\Support\Facades\Mail;

class MailService {
    public function sendMailToIndividial($request) {
        foreach($request->recipient as $recipient){
            Mail::to($recipient)
            ->send(new SendEmailToRecipients($request));
        }
    }

    public function sendMailToCollection($request) {
        return Mail::to($request->recipient)
                ->send(new SendEmailToRecipients($request));
    }
}