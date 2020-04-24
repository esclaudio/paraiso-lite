<?php

namespace App\Mails;

use App\Queue\SerializesModels;
use App\Models\Maintenance;
use App\Mailer\Mailable;

class MaintenanceReopenMail extends Mailable
{
    use SerializesModels;

    protected $maintenance;

    public function __construct(Maintenance $maintenance)
    {
        $this->maintenance = $maintenance;
    }

    public function build()
    {
        return $this->subject("Orden de mantenimiento reabierta")
            ->view('mails/maintenance_reopen.twig');
    }
}
