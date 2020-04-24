<?php

namespace App\Mails;

use App\Queue\SerializesModels;
use App\Models\Maintenance;
use App\Mailer\Mailable;

class MaintenanceInspectionMail extends Mailable
{
    use SerializesModels;

    protected $maintenance;

    public function __construct(Maintenance $maintenance)
    {
        $this->maintenance = $maintenance;
    }

    public function build()
    {
        return $this->subject("Inspección de orden de mantenimiento")
            ->view('mails/maintenance_inspection.twig');
    }
}
