<?php

namespace App\Mail;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Support\HtmlString;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendEmailToRecipients extends Mailable
{
    use Queueable, SerializesModels;

    public $request;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($request)
    {
        $this->request = $request;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        if($this->request->has('category')) {
            $headerData = [
                'category' => $this->request->category,
                // 'unique_args' => [
                //     'variable_1' => 'abc'
                // ]
            ];
    
            $header = $this->asString($headerData);
    
            $this->withSwiftMessage(function ($message) use ($header) {
                $message->getHeaders()
                        ->addTextHeader('X-SMTPAPI', $header);
            });
        }

        if($this->request->has('cc')) {
            $this->cc($this->request->cc);
        }
        if($this->request->has('bcc')) {
            $this->bcc($this->request->bcc);
        }

        $this->subject($this->request->subject)
        ->html(new HtmlString($this->request->content));


        if($this->request->has('attachment')) {
            if(!is_array($this->request->attachment)) {
                $this->attachFileToMail($this->request->attachment);
            } else {
                foreach($this->request->attachment as $attached) {
                    $this->attachFileToMail($attached);
                }
            }

        }
        return $this;
        // return $this->view('view.name');
    }

    private function asJSON($data)
    {
        $json = json_encode($data);
        $json = preg_replace('/(["\]}])([,:])(["\[{])/', '$1$2 $3', $json);

        return $json;
    }

    private function asString($data)
    {
        $json = $this->asJSON($data);

        return wordwrap($json, 76, "\n   ");
    }

    private function attachFileToMail($string_or_file) {
        
        if(@is_file($string_or_file)) {
            return $this->attach($string_or_file->getRealPath(), [
                        'as' => $string_or_file->getClientOriginalName(),
                        'mime' => $string_or_file->getMimeType(),
                    ]);
        } else {
            if(is_array($string_or_file) && array_key_exists('path', $string_or_file) && array_key_exists('filename', $string_or_file) && array_key_exists('contentType', $string_or_file)) {
               if (base64_encode(base64_decode($string_or_file['path'], true)) === $string_or_file['path']){
                    return $this->attachData($this->extractFile($string_or_file['path']), $string_or_file['filename'], [
                        'as' => $string_or_file['filename'],
                        'mime' => $string_or_file['contentType']
                    ]);
                } else {
                    throw new Exception('Path is expects a base64 encoded image format');
                } 
            } else {
                throw new Exception('Please include a file path or use a base64encoded data');
                
            }
           
        }
    }


    private function extractFile($source) {
        if(strpos($source, ',')) {
            @list($removed, $file) = explode(',', $source);
        } else {
            return base64_decode($source);
        }

        $decoded_file = base64_decode(($file));

        return $decoded_file;

    }
}
