<?php

namespace App\Mails;

use App\Mailer\Mailable;
use App\Models\ChangeManagement;

class ChangeManagementCreatedMail extends Mailable
{
    protected $change;

    public function __construct(ChangeManagement $change)
    {
        $this->change = $change;
    }

    public function build()
    {
        return $this->subject("Gestión de cambios creada")
            ->view('mails/change_management_created.twig');
    }
}
