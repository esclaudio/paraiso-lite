<?php

namespace App\Mails;

use App\Mailer\Mailable;
use App\Models\Complaint;

class ComplaintCreatedMail extends Mailable
{
    protected $complaint;

    public function __construct(Complaint $complaint)
    {
        $this->complaint = $complaint;
    }

    public function build()
    {
        return $this->subject("Reclamo creado")
            ->view('mails/complaint_created.twig');
    }
}
