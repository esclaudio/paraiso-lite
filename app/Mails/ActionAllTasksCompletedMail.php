<?php

namespace App\Mails;

use App\Queue\SerializesModels;
use App\Models\Action;
use App\Mailer\Mailable;

class ActionAllTasksCompletedMail extends Mailable
{
    use SerializesModels;

    protected $action;

    public function __construct(Action $action)
    {
        $this->action = $action;
    }

    public function build()
    {
        return $this->subject("Planificación de la verificación de acción pendiente")
            ->view('mails/action_all_tasks_completed.twig');
    }
}
