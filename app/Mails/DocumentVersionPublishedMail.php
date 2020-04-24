<?php

namespace App\Mails;

use App\Mailer\Mailable;
use App\Models\DocumentVersion;

class DocumentVersionPublishedMail extends Mailable
{
    protected $version;

    public function __construct(DocumentVersion $version)
    {
        $this->version = $version;
    }

    public function build()
    {
        return $this->subject("Documento publicado")
            ->view('mails/document_published.twig');
    }
}
