<?php

namespace App\Support\Mailer;

use Swift_Message;

class MessageBuilder
{
    /**
     * Message
     * 
     * @var Swift_Message
     */
    protected $swiftMessage;

    public function __construct(Swift_Message $swiftMessage)
    {
        $this->swiftMessage = $swiftMessage;
    }

    public function to($address, $name = null): MessageBuilder
    {
        if (is_array($address)) {
            $this->swiftMessage->setTo($address, $name);
        } else {
            $this->swiftMessage->addTo($address, $name);
        }

        return $this;
    }

    public function subject(string $subject): MessageBuilder
    {
        $this->swiftMessage->setSubject($subject);

        return $this;
    }

    public function body(string $body): MessageBuilder
    {
        $this->swiftMessage->setBody($body, 'text/html');

        return $this;
    }

    public function from(string $address, string $name = null): MessageBuilder
    {
        $this->swiftMessage->setFrom($address, $name);

        return $this;
    }

    public function getSwiftMessage(): Swift_Message
    {
        return $this->swiftMessage;
    }
}
