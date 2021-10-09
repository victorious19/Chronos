<?php

namespace App\Http\Controllers;

use App\Mail\SendMail;
use App\Models\Calendar;
use App\Models\Invite;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class InviteController extends Controller
{
    function create(Request $request, $calendar_id) {
        $request->validate(['user_id'=> 'exists:users,id']);
        $calendar = Calendar::find($calendar_id);
        if (empty($calendar)) return response('Calendar not found', 404);
        $invite = auth()->user()->invite($calendar_id);
        if($calendar->user()->id != auth()->user()->id && empty($invite) || $calendar->user()->id == $request->user_id) return response('Access denied', 403);
        $user = User::find($request->user_id);
        $user_invite = $user->invite($calendar_id);
        if($user_invite) $invite->delete();
        $data = [
            'title' => 'Calendar invite',
            'hi' => 'Hello '.$user->login."!",
            'content' => 'Go to this link if you want add calendar "'.$calendar->title.'" from '.auth()->user()->login.
                ': http://localhost:8080/home?add_calendar='.$calendar_id
        ];
        Mail::to($user->email)->send(new SendMail($data));
        return Invite::create([
            'calendar_id' => $calendar_id,
            'user_id' => $request->user_id
        ]);
    }
    function update($calendar_id) {
        $invite = auth()->user()->invite($calendar_id);
        if(empty($invite)) return response('Invite not found', 404);
        $invite->update(['status' => 'passive']);
    }
    function delete($calendar_id) {
        $invite = auth()->user()->invite($calendar_id);
        if(empty($invite)) return response('Invite not found', 404);
        return $invite->delete();
    }
}
