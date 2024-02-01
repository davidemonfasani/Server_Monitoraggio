<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MoniError extends Mailable
{
    
    use Queueable, SerializesModels;

    public $errors;

    public function __construct($errors)
    {
        $this->errors = $errors;
    }

    public function build()
    {
        return $this->view('emails.validationerror')
                    ->with(['errors' => $this->errors]);
    }

    public function envelope ()  
    {  
      return new Envelope (  
       
        subject: 'Order Shipped',  
      );  
    }   

public function content(): Content
{
    return new Content(
        view: 'mail.orders.shipped',
        text: 'mail.orders.shipped-text'
    );
}
    

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
