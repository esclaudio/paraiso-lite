<?php

namespace App\Mails;

use App\Queue\SerializesModels;
use App\Models\Maintenance;
use App\Mailer\Mailable;

class MaintenanceCreatedMail extends Mailable
{
    use SerializesModels;

    protected $maintenance;

    public function __construct(Maintenance $maintenance)
    {
        $this->maintenance = $maintenance;
    }

    public function build()
    {
        return $this->subject("Nueva orden de mantenimiento")
            ->view('mails/maintenance_created.twig');
    }
}
