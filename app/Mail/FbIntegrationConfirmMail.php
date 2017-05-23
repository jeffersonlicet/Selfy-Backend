<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class FbIntegrationConfirmMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;
    private $user;
    private $code;

    /**
     * Create a new message instance.
     *
     * @param User $user
     */
    public function __construct(User $user, $code)
    {
        $this->user = $user;
        $this->code = $code;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return
            $this->from('hello@sprkly.net', 'Selfy')->subject("Selfy - Confirm Facebook integration")->markdown('vendor.mail.facebook.confirmation.template')
            ->with([
                'greeting' => 'Hi '.$this->user->firstname ?? $this->user->username,
                'introLines' => ["We've sent you this email to confirm the integration of your existing Selfy account with your Facebook profile.", "If you want to link your accounts simply press the button below."],
                'actionUrl' => 'https://n98va.app.goo.gl/?link=http://www.getselfy.net/facebook/link?code='.$this->code.'&apn=com.sparkly.selfy',
                'actionText' => 'Confirm',
                'outroLines' => ["If you don't wish to link your accounts please ignore this email.", "Thank you very much for using Selfy."]
            ]);
    }
}
