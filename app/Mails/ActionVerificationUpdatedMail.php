<?php

namespace App\Mails;

use App\Mailer\Mailable;
use App\Models\ActionVerification;

class ActionVerificationUpdatedMail extends Mailable
{
    protected $verification;

    public function __construct(ActionVerification $verification)
    {
        $this->verification = $verification;
    }

    public function build()
    {
        return $this->subject("Planificación de la verificación modificada")
            ->view('mails/action_verification_updated.twig');
    }
}
