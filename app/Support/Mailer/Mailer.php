<?php

namespace App\Support\Mailer;

use Swift_Message;
use Swift_Mailer;
use Slim\Views\Twig;
use App\Queue\Queue;
use App\Mailer\Contracts\MailerContract;
use App\Mailer\Contracts\MailableContract;
use App\Jobs\SendMail;

class Mailer implements MailerContract
{
    protected $swift;
    protected $twig;
    protected $queue;
    protected $from = [];

    public function __construct(Swift_Mailer $swift, Twig $twig, Queue $queue)
    {
        $this->swift = $swift;
        $this->twig = $twig;
        $this->queue = $queue;
    }

    public function alwaysFrom(string $address, string $name = null)
    {
        $this->from = compact('address', 'name');

        return $this;
    }

    public function to($address, $name = null)
    {
        return (new PendingMailable($this))->to($address, $name);
    }

    public function send(MailableContract $mail)
    {
        $message = $this->buildMessage($mail);

        return $this->swift->send($message);
    }

    public function queue(MailableContract $mail)
    {
        $this->queue->dispatch(new SendMail($mail));
    }

    protected function sendMailable(Mailable $mailable)
    {
        return $mailable->send($this);
    }

    protected function buildMessage(MailableContract $mail): Swift_Message
    {
        $message = (new MessageBuilder(new Swift_Message))
            ->from($this->from['address'], $this->from['name']);

        $mail->buildMessage($message);

        $body = $this->parseView($mail->view, $mail->getViewData());

        $message->body($body);

        return $message->getSwiftMessage();
    }

    public function parseView(string $view, array $data = []): string
    {
        return $this->twig->fetch($view, $data);
    }
}
