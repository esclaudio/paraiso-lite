<?php

namespace App\Support\Mailer;

use Slim\Views\Twig;
use App\Mailer\MessageBuilder;
use App\Mailer\Mailer;
use App\Mailer\Contracts\MailableContract;

abstract class Mailable implements MailableContract
{
    protected $to = [];
    protected $from = [];
    protected $subject;
    protected $viewData = [];

    public $view;

    public abstract function build();

    public function buildMessage(MessageBuilder $message)
    {
        $this->build();

        $message->to($this->to['address'], $this->to['name'])
            ->subject($this->subject);

        if ($this->from) {
            $message->from($this->from['address'], $this->from['name']);
        }
    }

    public function getViewData(): array
    {
        $data = $this->viewData;

        foreach(get_object_vars($this) as $name => $value) {
            $data[$name] = $value;
        }

        return $data;
    }

    public function to($address, $name = null): Mailable
    {
        $this->to = compact('address', 'name');

        return $this;
    }

    public function from(string $address, string $name = null): Mailable
    {
        $this->from = compact('address', 'name');

        return $this;
    }

    public function view(string $view)
    {
        $this->view = $view;

        return $this;
    }

    public function with(array $viewData = [])
    {
        $this->viewData = $viewData;

        return $this;
    }

    public function subject(string $subject)
    {
        $this->subject = $subject;

        return $this;
    }
}
