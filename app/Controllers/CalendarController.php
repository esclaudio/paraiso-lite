<?php

namespace App\Controllers;

use Slim\Http\Response;
use Slim\Http\Request;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Calendar;

class CalendarController extends Controller
{
    /**
     * Index
     *
     * @param  \Slim\Http\Request  $request
     * @param  \Slim\Http\Response $response
     * @param  array               $args
     *
     * @return \Slim\Http\Response
     */
    public function index(Request $request, Response $response): Response
    {
        $startDate = new Carbon($request->getParam('start'));
        $endDate = new Carbon($request->getParam('end'));
        
        $events = (new Calendar($this->user, $this->router))
            ->loadEventsFrom($startDate, $endDate)
            ->toArray();

        return $response->withJson($events);
    }

    public function ics(Request $request, Response $response, array $args): Response
    {
        $user = User::where('calendar_token', $args['token'])->firstOrFail();
        
        $startDate = new Carbon(date('Y-01-01'));
        $endDate = new Carbon(date('Y-12-31'));

        $events = (new Calendar($user, $this->router))
            ->loadEventsFrom($startDate, $endDate)
            ->toICal($this->container->settings['app_url']);

        return $response
            ->withHeader('Content-Type', 'text/calendar; charset=utf-8')
            ->write($events->render());
    }

    public function token(Request $request, Response $response): Response
    {
        if ( ! $this->user->calendar_token || $request->getParam('generate', 0)) {
            $this->user->generateCalendarToken();
        }

        $token = $this->user->calendar_token;
        $url = $this->container->settings['app_url'] . $this->pathFor('calendar.ics', ['token' => $token]);

        return $response->withJson([
            'token' => $token,
            'url'   => $url,
        ]);
    }
}