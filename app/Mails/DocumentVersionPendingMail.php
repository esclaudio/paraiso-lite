<?php

namespace App\Mails;

use App\Mailer\Mailable;
use App\Models\DocumentVersion;

class DocumentVersionPendingMail extends Mailable
{
    protected $version;

    public function __construct(DocumentVersion $version)
    {
        $this->version = $version;
    }

    public function build()
    {
        return $this->subject("Documento pendiente")
            ->view('mails/document_pending.twig');
    }
}
