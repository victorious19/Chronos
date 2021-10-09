<?php

namespace App\Http\Controllers;

use App\Models\Calendar;
use DateTime;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    function create(Request $request) {
        $request->validate(['title' => 'required|string']);
        return Calendar::create(['user_id' => auth()->user()->id, 'title' => $request->title]);
    }
    function getAll() {
        $calendars = [];
        $calendars['normal'] = auth()->user()->calendars();
        $invites = auth()->user()->invites();

        foreach ($invites as $invite) {
            $calendar = $invite->calendar();
            $calendar->is_active = $invite->status == 'active';
            $calendar->is_invite = true;
            $calendars['invited'][] = $calendar;
        }

        return $calendars;
    }
    function update(Request $request, $calendar_id) {
        $request->validate(['is_active'  => 'required|boolean']);
        $calendar = Calendar::find($calendar_id);
        if (empty($calendar)) return response('Calendar not found', 404);
        $invite = auth()->user()->invite($calendar_id);
        if(isset($invite)) {
            $status = $request->is_active?'active':'passive';
            return $invite->update(['status' => $status]);
        }
        if($calendar->user()->id != auth()->user()->id) return response('Access denied', 403);
        return $calendar->update($request->all());
    }
    function delete($calendar_id) {
        $calendar = Calendar::find($calendar_id);
        if (empty($calendar)) return response('Calendar not found', 404);
        if($calendar->user()->id != auth()->user()->id) return response('Access denied', 403);
        return $calendar->delete();
    }
}
