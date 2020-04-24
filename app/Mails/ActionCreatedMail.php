<?php

namespace App\Mails;

use App\Queue\SerializesModels;
use App\Models\Action;
use App\Mailer\Mailable;

class ActionCreatedMail extends Mailable
{
    use SerializesModels;

    protected $action;

    public function __construct(Action $action)
    {
        $this->action = $action;
    }

    public function build()
    {
        return $this->subject("Nueva acción")
            ->view('mails/action_created.twig');
    }
}
