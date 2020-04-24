<?php

namespace App\Mails;

use App\Mailer\Mailable;
use App\Models\ActionTask;

class ActionTaskCompletedMail extends Mailable
{
    protected $task;

    public function __construct(ActionTask $task)
    {
        $this->task = $task;
    }

    public function build()
    {
        return $this->subject("Medida completada")
            ->view('mails/action_task_completed.twig');
    }
}
