<?php

namespace App\Http\Controllers\Api\Admin\Mail;

use App\Http\Controllers\Controller;
use App\Http\Requests\MailRequest;
use App\Mail\SendEmailToRecipients;
use App\Services\Mail\MailService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class MailController extends Controller
{
    public function __invoke(MailRequest $request, MailService $mail)
    {

        if($request->has('pattern') && $request->pattern === 'individual') {
            try {
                $mail->sendMailToIndividial($request);
            } catch(Exception $e) {
                return response()->errorResponse('Failed to send email', $e->getMessage());
            }
        } else {
            try {
                $mail->sendMailToCollection($request);
            } catch(Exception $e) {
                return response()->errorResponse('Failed to send email', $e->getMessage());
            }
        }

        if(Mail::failures()) {
            return response()->errorResponse('Failed sendmail mail', Mail::failures());
        }

        return response()->success("Mail sent successfully");
    }
}
