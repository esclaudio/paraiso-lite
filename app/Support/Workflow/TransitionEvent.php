<?php

namespace App\Support\Workflow;

class TransitionEvent
{
    protected $blocked;
    protected $transition;
    protected $subject;

    public function __construct(Transition $transition, Contracts\StatefulContract $subject)
    {
        $this->blocked    = false;
        $this->transition = $transition;
        $this->subject    = $subject;
    }

    public function isBlocked(): bool
    {
        return $this->blocked;
    }

    public function setBlocked($blocked): void
    {
        $this->blocked = $blocked;
    }

    public function getSubject(): Contracts\StatefulContract
    {
        return $this->subject;
    }

    public function getTransition(): Transition
    {
        return $this->transition;
    }
}
