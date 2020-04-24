<?php

namespace App\Models;

use Slim\Router;
use Eluceo\iCal\Component\Event as IEvent;
use Eluceo\iCal\Component\Calendar as ICalendar;
use Carbon\Carbon;
use App\Models\User;

class Calendar
{
    /**
     * Router
     *
     * @var \Slim\Router
     */
    protected $router;

    /**
     * User
     *
     * @var \App\Models\User
     */
    protected $user;

    /**
     * Events
     *
     * @var \Illuminate\Support\Collection
     */
    protected $events;

    public function __construct(User $user, Router $router)
    {
        $this->router = $router;
        $this->user = $user;
    }

    public function toArray(): array
    {
        if ( ! $this->events) {
            return [];
        }

        return $this->events->toArray();
    }

    public function toICal(string $id): ICalendar
    {
        $ical = new ICalendar($id);

        $this->events->each(function ($event) use ($ical) {
            $ical->addComponent(
                (new IEvent)
                    ->setDtStart(new \DateTime($event['start']))
                    ->setDtEnd(new \DateTime($event['start']))
                    ->setNoTime(true)
                    ->setSummary($event['description'])
            );
        });

        return $ical;
    }

    public function loadEventsFrom(Carbon $startDate, Carbon $endDate)
    {
        return $this;
    }
}