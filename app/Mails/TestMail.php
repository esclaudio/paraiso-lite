<?php

namespace App\Mails;

use App\Mailer\Mailable;
use App\Models\DocumentVersion;

class TestMail extends Mailable
{
    public function build()
    {
        return $this->subject("Prueba")->view('mails/test.twig');
    }
}
