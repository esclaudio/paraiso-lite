<?php

namespace App\Support\Mailer;

class PendingMailable
{
    /**
     * Mailer
     * 
     * @var App\Mailer\Mailer
     */
    protected $mailer;

    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    public function to($address, $name = null): PendingMailable
    {
        $this->to = compact('address', 'name');

        return $this;
    }

    public function send(Mailable $mailable)
    {
        $mailable->to($this->to['address'], $this->to['name']);

        return $this->mailer->send($mailable);
    }

    public function queue(Mailable $mailable)
    {
        $mailable->to($this->to['address'], $this->to['name']);
    
        return $this->mailer->queue($mailable);
    }
}
