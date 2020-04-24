<?php

namespace App\Mails;

use App\Queue\SerializesModels;
use App\Models\Risk;
use App\Mailer\Mailable;

class RiskAllTasksCompletedMail extends Mailable
{
    use SerializesModels;

    protected $risk;

    public function __construct(Risk $risk)
    {
        $this->risk = $risk;
    }

    public function build()
    {
        return $this->subject("Evaluación de riesgo/oportunidad pendiente")
            ->view('mails/risk_all_tasks_completed.twig');
    }
}
