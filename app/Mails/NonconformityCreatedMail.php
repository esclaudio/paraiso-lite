<?php

namespace App\Mails;

use App\Mailer\Mailable;
use App\Models\Nonconformity;

class NonconformityCreatedMail extends Mailable
{
    protected $nonconformity;

    public function __construct(Nonconformity $nonconformity)
    {
        $this->nonconformity = $nonconformity;
    }

    public function build()
    {
        return $this->subject("Salida no conforme creada")
            ->view('mails/nonconformity_created.twig');
    }
}
