<?php

namespace App\Http\Controllers;

use App\Models\Calendar;
use DateTime;
use Illuminate\Http\Request;
use App\Models\Event;

class EventController extends Controller
{
    function getAll() {
        $calendars = auth()->user()->active_calendars();
        $invites = auth()->user()->active_invites();
        $events = [];

        foreach ($invites as $invite) {
            $calendars[] = $invite->calendar();
        }

        foreach ($calendars as $calendar) {
            foreach ($calendar->events() as $event) {
                $events[] = $this->more_info($event);
            }
        }

        return $events;
    }
    function more_info($event) {
        $start = (new DateTime($event->start))->getTimestamp();
        $end = (new DateTime($event->end))->getTimestamp();
        $event->allDay = $end % 86400 == 0 && $start % 86400 == 0;
        return $event;
    }
    function getById($event_id) {
        $event = Event::find($event_id);
        if (empty($event)) return response('Event not found', 404);
        $invite = auth()->user()->invite($event->calendar_id);
        if($event->calendar()->user()->id != auth()->user()->id && empty($invite)) return response('Access denied', 403);
        return $event;
    }
    function create(Request $request) {
        $request->validate([
            'category' => 'in:arrangement,reminder,task',
            'start' => 'required|string',
            'end' => 'required|string',
            'title' => 'required|string',
            'color' => 'string'
        ]);
        $active_calendars = auth()->user()->active_calendars();
        $invites = auth()->user()->active_invites();

        if(count($active_calendars)) $request["calendar_id"] = $active_calendars[0]->id;
        else if(count($invites)) $request["calendar_id"] = $invites[0]->calendar()->id;
        else return response('Calendar not found', 404);

        $request->start = new DateTime($request->start);
        $request->end = new DateTime($request->end);

        return Event::create($request->all());
    }
    function update(Request $request, $event_id) {
        $request->validate([
            'category' => 'in:arrangement,reminder,task',
            'start' => 'string',
            'end' => 'string',
            'title' => 'string',
            'color' => 'string'
        ]);
        $event = Event::find($event_id);
        if (empty($event)) return response('Event not found', 404);
        $invite = auth()->user()->invite($event->calendar_id);
        if($event->calendar()->user()->id != auth()->user()->id && empty($invite)) return response('Access denied', 403);

        $request->start = new DateTime($request->start);
        $request->end = new DateTime($request->end);

        return $event->update($request->all());
    }
    function delete($event_id) {
        $event = Event::find($event_id);
        if (empty($event)) return response('Event not found', 404);
        $invite = auth()->user()->invite($event->calendar_id);
        if($event->calendar()->user()->id != auth()->user()->id && empty($invite)) return response('Access denied', 403);
        return $event->delete();
    }
}
