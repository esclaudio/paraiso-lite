<?php

namespace App\Support\Mailer;

use App\Mailer\Contracts\MailerContract;
use App\Mailer\Contracts\MailableContract;

class FakeMailer implements MailerContract
{
    public function alwaysFrom(string $address, string $name = null)
    {
        //
    }

    public function to($address, $name = null)
    {
        //
    }

    public function send(MailableContract $mail)
    {
        //
    }

    public function queue(MailableContract $mail)
    {
        //
    }
}
