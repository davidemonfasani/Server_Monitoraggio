<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MoniError extends Mailable
{
    use Queueable, SerializesModels;

    public $msg;

    public function __construct($msg)
    {
        $this->msg = $msg;
    }

    public function build()
    {
        return $this->subject('Errori in un msg di monitoraggio')
                    ->from( env('MAIL_FROM_ADDRESS'),  env('MAIL_FROM_NAME'))
                    ->text('emails.validationerror_plain')
                    >with([
                        'msg' => $this->msg
                    ]);
    }
}
