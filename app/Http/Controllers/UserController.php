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

    public function accept($token)
    {
        $verify = Verify::where('created_at', '>=', $this->now->subDays(1)->toDateTimeString())
        ->where('token', $token)->first();
        if (!is_null($verify)) {
            $user = User::where('id', $verify->user_id)->first();
            if (is_null($user->email_verified_at)) {
                $user->update([
                    'email_verified_at' => $this->now,
                ]);
                return 'Your account has been verified. You can close this window.';
            }
            $verify->delete();
            return 'Your account already verified!';
        }
        return 'Token expired!';
    }
}
