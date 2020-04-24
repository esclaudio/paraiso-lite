<?php

namespace App\Mails;

use App\Mailer\Mailable;
use App\Models\ActionTask;

class ActionTaskUpdatedMail extends Mailable
{
    protected $task;

    public function __construct(ActionTask $task)
    {
        $this->task = $task;
    }

    public function build()
    {
        return $this->subject("Medida modificada")
            ->view('mails/action_task_updated.twig');
    }
}
