<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Verify;
use Carbon\Carbon;

class UserController extends Controller
{
    public function __construct()
    {
        $this->now = Carbon::now();
    }

    public function accept($date, $token)
    {
        $verify = Verify::whereDate('created_at', $date)->where('token', $token)->first();
        if (!is_null($verify)) {
            $user = User::where('id', $verify->user_id)->first();
            if (is_null($user->email_verified_at)) {
                $user->update([
                    'email_verified_at' => $this->now,
                ]);
                return 'Your account has been verified. You can close this window.';
            }
            $verify->delete();
            return 'Token expired!';
        }
        return 'URL not found!';
    }
}
