<?php

namespace App\Mails;

use App\Mailer\Mailable;
use App\Models\ActionVerification;

class ActionVerificationDeletedMail extends Mailable
{
    protected $verification;

    public function __construct(ActionVerification $verification)
    {
        $this->verification = $verification;
    }

    public function build()
    {
        return $this->subject("Planificación de la verificación eliminada")
            ->view('mails/action_verification_deleted.twig');
    }
}
