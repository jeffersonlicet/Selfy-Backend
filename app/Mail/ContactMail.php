<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ContactMail extends Mailable
{
    use Queueable, SerializesModels;
    private $email;
    private $body;
    private $name;

    /**
     * Create a new message instance.
     *
     * @param $name
     * @param $email
     * @param $body
     */
    public function __construct($name, $email, $body)
    {
        $this->name = $name;
        $this->email = $email;
        $this->body = $body;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return
            $this->to('hello@sprkly.net')->from('hello@sprkly.net', 'Selfy contact')->subject("Selfy - Contact form")->markdown('vendor.mail.contact')
                ->with([
                    'greeting' => 'New message from '. $this->name . ' ('.$this->email.')',
                    'body' => $this->body,
                ]);
    }
}
