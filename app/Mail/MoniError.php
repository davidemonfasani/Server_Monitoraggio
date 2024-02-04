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
    public string $obj;

    public function __construct($msg,$obj)
    {
        $this->msg = $msg;
        $this->obj = $obj;
    }

    public function build()
    {
        return $this->subject($this->obj)
                    ->text('emails.validationerror_plain')
                    >with([
                        'msg' => $this->msg
                    ]);
    }
}
