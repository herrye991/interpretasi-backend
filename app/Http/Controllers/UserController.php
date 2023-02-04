<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use App\Models\PasswordReset;
use App\Models\User;
use App\Models\Verify;
use Carbon\Carbon;
use Illuminate\Http\Request;

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

    public function reset($token)
    {
        $reset = PasswordReset::where('created_at', '>=', $this->now->subMinutes(15)->toDateTimeString())
        ->where('token', $token)->first();
        if (!is_null($reset)) {
            return view("auth.password-reset", compact('token'));
        }
        return 'Token expired!';
    }

    public function resetPost(Request $request, $token)
    {
        $request->validate([
            'password' => 'confirmed|min:6'
        ]);
        $reset = PasswordReset::where('created_at', '>=', $this->now->subMinutes(15)->toDateTimeString())
        ->where('token', $token)->first();
        if (!is_null($reset)) {
            User::where('email', $reset->email)->update([
                'password' => Hash::make($request->password)
            ]);
            $reset->delete();
            return 'Password Updated!';
        }
        return 'Token expired!';
    }
}
