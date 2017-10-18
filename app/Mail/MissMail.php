<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class MissMail extends Mailable
{
    use Queueable, SerializesModels;

    private $name = '';

    /**
     * Create a new message instance.
     *
     * @param $name
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('hello@sprkly.net', 'Selfy')->subject("Selfy ". (empty($this->name) ? '- Selfier' : '- '.$this->name))->markdown('vendor.mail.miss')->with(['name' => $this->name]);
    }
}
