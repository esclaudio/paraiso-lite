<?php

namespace App\Support\Workflow;

use App\Workflow\Contracts\StatefulContract;

class Workflow
{
    protected $states      = [];
    protected $transitions = [];
    protected $events      = [];

    /**
     * Add a new state
     * @param string $name       Name
     * @param string $type       Type
     * @param array  $properties Properties
     */
    public function addState(string $name, string $type, array $properties = [])
    {
        $this->states[$name] = new State($name, $type, $properties);
    }

    /**
     * Add a new transition
     * @param string $name          Name
     * @param string $fromStateName From state name
     * @param string $toStateName   To state name
     * @param array  $properties    Properties
     */
    public function addTransition(string $name, string $fromStateName, string $toStateName, array $properties = [])
    {
        $fromState = $this->getState($fromStateName);
        $toState   = $this->getState($toStateName);

        $this->transitions[$name] = new Transition($name, $fromState, $toState, $properties);
    }

    /**
     * Listen
     * @param string   $eventName Event name
     * @param callable $callback  Callback
     */
    public function listen(string $eventName, callable $callback) {
        $this->events[$eventName] = $callback;
    }

    /**
     * Return if an subject can perform a transition
     * @param  StatefulContract $subject        Subject
     * @param  string           $transitionName Transition
     * @return bool
     */
    public function can(StatefulContract $subject, string $transitionName): bool
    {
        return $this->doCan($subject, $this->getTransition($transitionName));
    }

    /**
     * Apply a transition to a subject
     * @param  StatefulContract $subject        Subject
     * @param  string           $transitionName Transition
     * @return void
     */
    public function apply(StatefulContract $subject, string $transitionName)
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
     * @param  StatefulContract $subject Subject
     * @return array
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
     * @param  StatefulContract $subject    Subject
     * @param  Transition       $transition Transition
     * @param  array            $context    Context
     * @return bool
     */
    protected function doCan(StatefulContract $subject, Transition $transition): bool
    {
        return $subject->getState() == $transition->getFrom()->getName() &&
            $this->doEvent('before', $transition, $subject);
    }

    /**
     * Do event
     * @param  string           $eventName  Event name
     * @param  Transition       $transition Transition
     * @param  StatefulContract $subject    Subject
     * @return
     */
    protected function doEvent(string $eventName, Transition $transition, StatefulContract $subject)
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
     * @param  string $stateName State name
     * @return State
     */
    public function getState(string $stateName) : State
    {
        if (isset($this->states[$stateName])) {
            return $this->states[$stateName];
        }

        throw new \Exception(sprintf('State "%s" not found', $stateName));
    }

    /**
     * Get a transition
     * @param  string $transitionName Transition name
     * @return Transition
     */
    public function getTransition(string $transitionName) : Transition
    {
        if (isset($this->transitions[$transitionName])) {
            return $this->transitions[$transitionName];
        }

        throw new \Exception(sprintf('Transition "%s" not found', $transitionName));
    }
}
