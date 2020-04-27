<?php

namespace App\Support\Workflow;

use App\Support\Workflow\Contracts\StatefulContract;

class Workflow
{
    protected $states      = [];
    protected $transitions = [];
    protected $events      = [];

    /**
     * Add a new state
     */
    public function addState(string $name,array $properties = []): void
    {
        $this->states[$name] = new State($name, $properties);
    }

    /**
     * Add a new transition
     */
    public function addTransition(string $name, string $fromState, string $toState, array $properties = []): void
    {
        $this->transitions[$name] = new Transition($name, $this->getState($fromState), $this->getState($toState), $properties);
    }

    /**
     * Listen
     */
    public function listen(string $eventName, callable $callback): void
    {
        $this->events[$eventName] = $callback;
    }

    /**
     * Return if an subject can perform a transition
     */
    public function can(StatefulContract $subject, string $transitionName): bool
    {
        return $this->doCan($subject, $this->getTransition($transitionName));
    }

    /**
     * Apply a transition to a subject
     */
    public function apply(StatefulContract $subject, string $transitionName): void
    {
        $transition = $this->getTransition($transitionName);

        if (!$this->doCan($subject, $transition)) {
            throw new \Exception(sprintf('Can not apply transition "%s"', $transition->getName()));
        }

        $subject->setState($transition->getTo()->getName());
        $this->doEvent('after', $transition, $subject);
    }

    /**
     * Get an array of allowed transition for a subject
     */
    public function getAllowedTransitions(StatefulContract $subject): array
    {
        $allowed = [];

        foreach($this->transitions as $transition) {
            if ($this->doCan($subject, $transition)) {
                $allowed[] = $transition;
            }
        }

        return $allowed;
    }

    /**
     * Do can
     */
    protected function doCan(StatefulContract $subject, Transition $transition): bool
    {
        return $subject->getState() == $transition->getFrom()->getName() &&
            $this->doEvent('before', $transition, $subject);
    }

    /**
     * Do event
     */
    protected function doEvent(string $eventName, Transition $transition, StatefulContract $subject): bool
    {
        if (!isset($this->events[$eventName])) {
            return true;
        }

        $callback = $this->events[$eventName];
        $event    = new TransitionEvent($transition, $subject);

        call_user_func($callback, $event);

        return !$event->isBlocked();
    }

    /**
     * Get a state
     */
    public function getState(string $stateName): State
    {
        if (isset($this->states[$stateName])) {
            return $this->states[$stateName];
        }

        throw new \Exception(sprintf('State "%s" not found', $stateName));
    }

    /**
     * Get a transition
     */
    public function getTransition(string $transitionName): Transition
    {
        if (isset($this->transitions[$transitionName])) {
            return $this->transitions[$transitionName];
        }

        throw new \Exception(sprintf('Transition "%s" not found', $transitionName));
    }
}
