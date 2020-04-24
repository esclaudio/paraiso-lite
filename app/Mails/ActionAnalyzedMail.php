<?php

namespace App\Mails;

use App\Queue\SerializesModels;
use App\Models\Action;
use App\Mailer\Mailable;

class ActionAnalyzedMail extends Mailable
{
    use SerializesModels;

    protected $action;

    public function __construct(Action $action)
    {
        $this->action = $action;
    }

    public function build()
    {
        return $this->subject("Acción analizada")
            ->view('mails/action_analyzed.twig');
    }
}
