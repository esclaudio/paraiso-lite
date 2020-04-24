<?php

namespace App\Mails;

use App\Mailer\Mailable;
use App\Models\ActionTask;

class ActionTaskDeletedMail extends Mailable
{
    protected $task;

    public function __construct(ActionTask $task)
    {
        $this->task = $task;
    }

    public function build()
    {
        return $this->subject("Medida eliminada")
            ->view('mails/action_task_deleted.twig');
    }
}
