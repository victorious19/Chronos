<?php

namespace App\Http\Controllers;

use App\Mail\SendMail;
use App\Models\Calendar;
use App\Models\PasswordReset;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class UserController extends Controller
{
    function register(Request $request) {
        $request->validate([
            'login' => 'required|string|unique:users,login|max:30',
            'password'=>'required|confirmed|min:8',
            'email'=>'required|string|unique:users,email|max:255'
        ]);
        $request['password'] = bcrypt($request['password']);
        $user = User::create($request->all());
        $token = $user->createToken('chronos')->plainTextToken;
        Calendar::create([
            'user_id' => $user->id,
            'title' => $user->login,
            'is_active' => 1,
            'is_default' => 1
        ]);

        $response = [
            'user' => $user,
            'token' => $token
        ];
        return response($response, 201);
    }
    function login(Request $request)
    {
        $request->validate([
            'login' => 'required|string|max:30',
            'password'=>'required|string|min:8'
        ]);
        $auth = $request->only(['login', 'password']);
        if (empty($auth['login']) or empty($auth['password'])) {
            return response(['message' => "Empty fields"], 422);
        }
        $user = User::where('login', $auth['login'])->first();
        if (empty($user)) $user = User::where('email', $auth['login'])->first();
        if (empty($user) or !Hash::check($auth['password'], $user->password)) {
            return response(['message' => "Bad login or password"], 400);
        }
        $token = $user->createToken('chronos')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token
        ];
        return response($response, 201);
    }
    static function logout() {
        auth()->user()->tokens()->delete();
        return [
            'message' => 'Logged out'
        ];
    }
    function passwordReset(Request $request) {
        $request->validate(['email'=>'required|string']);
        $email = $request->input('email');
        $user = User::where('email', $email)->first();
        if (empty($user)) return response(['message' => 'Email does not exist!'], 404);
        $verification_code = Str::random(6);
        $data = [
            'title' => 'Your password',
            'hi' => 'Hello '.$user->login."!",
            'content' => 'Verification code: '.$verification_code
        ];
        Mail::to($email)->send(new SendMail($data));
        $password_resets = PasswordReset::where('email', $email)->get();
        if ($password_resets) {
            foreach ($password_resets as $password_reset) {
                $password_reset->delete();
            }
        }
        PasswordReset::create([
            'email' => $email,
            'verification_code' => $verification_code
        ]);

        return 'success';
    }
    function passwordChange(Request $request) {
        $request->validate([
            'email' => 'required|exists:users,email',
            'verification_code' => 'required|size:6',
            'password' => 'required|confirmed|min:8',
        ]);
        $password_reset = PasswordReset::where('email', $request->email)->first();
        if ($password_reset->verification_code != $request->verification_code)
            return response(['errors'=>['verification_code'=>["Bad verification code"]]], 400);
        $dif = (new \DateTime("now"))->getTimestamp() - (new \DateTime($password_reset->created_at))->getTimestamp();
        if ($dif > 120)
            return response(['message'=>"Verification code is bad or outdated"], 400);
        $user = User::where('email', $request->email)->first();
        $user->update(['password' => bcrypt($request->password)]);
        return $user;
    }
    function getAllWithoutMe() {
        return User::where('id', '!=', auth()->user()->id)->get();
    }
}
