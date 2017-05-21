<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class FbIntegrationConfirmMail extends Mailable //implements ShouldQueue
{
    use Queueable, SerializesModels;
    private $user;

    /**
     * Create a new message instance.
     *
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return
            $this->from('hello@sprkly.net')
            ->markdown('vendor.mail.facebook.confirmation.template')
            ->with([
                'greeting' => 'Hi '.$this->user->firstname ?? $this->user->username,
                'introLines' => ["We have sent you this email to confirm the integration of your existing Selfy account with your Facebook profile.", "If you want to link the accounts simply press the button below."],
                'code' => 'gag',
                'actionUrl' => 'selfy::confirm_facebook_integration',
                'actionText' => 'Confirm',
                'outroLines' => ["If you do not wish to link accounts please ignore this email.", "Thank you very much for using Selfy."]
            ]);
    }
}
