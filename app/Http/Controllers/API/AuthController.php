<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Helpers\ResponseFormatter;
use App\Models\Verify as VerifyData;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\Verify;
use Auth;

class AuthController extends Controller
{
    public function __construct() {
        $this->now = Carbon::now();
        $this->token = bin2hex(base64_encode($this->now));
        if (!empty(auth('api')->user())) {
            $this->user = auth('api')->user();
        }
    }

    public function signup(Request $request)
    {
        $request->validate([
            'email' => 'email|required',
        ]);
        $email = $request->email;
        $user = User::where('email', $email)->first();
        if (!is_null($user)) {
            if (!is_null($user->email_verified_at)) {
                return ResponseFormatter::error('User already exist.', 403);
            }
        } else {
            $user = User::create([
                'email' => $email,
            ]);
        }
        $this->verify($request->email);
        $accessToken = $user->createToken('authToken')->accessToken;        
        return ResponseFormatter::success($accessToken, 200, 200);
    }

    public function resend()
    {
        $user = $this->user;
        $this->verify($user->email);
        return ResponseFormatter::success('Email Sended', 200, 200);
    }

    public function signin(Request $request)
    {
        $request->validate([
            'email' => 'email|required',
            'password' => 'required'
        ]);
                
        $user = User::where('email', $request->email)->first();
        if (empty($user)) {
            return ResponseFormatter::error('User not found!', 404, 404);
        }
        $credentials = Request(['email', 'password']);
        if (!Auth::attempt($credentials)) {
            return ResponseFormatter::error('Wrong password!', 403, 403);
        }
        
        $accessToken = $user->createToken('authToken')->accessToken;        
        return ResponseFormatter::success($accessToken, 200, 200);
    }

    public function signout(Request $request)
    {
        request()->header("Accept", "application/json");
        $request->user('api')->token()->revoke();
        return ResponseFormatter::success('Token revoked', 200);
    }

    public function oauth($provider)
    {
        $date = $this->now->format('Y-m-d H:i');
        $token = hash('sha256', $date);
        $email = request()->email;
        $display_name = request()->displayName;
        $photo = request()->photo;
        $user = User::where('email', $email)->first();
        $photo = $photo === null ? '' : $photo;
        if ($provider == 'google') {
            if ($token == request()->token) {
                if (empty($user)) {
                    // Create new user
                    $name = explode(' ', trim($display_name));
                    if (count($name) == 2) {
                        $first_name = $name[0];
                        $last_name = $name[1];
                    } else {
                        $first_name = $name[0];
                        $last_name = '';
                    }
                    $user = User::create([
                        'name' => $first_name . ' ' . $last_name,
                        'email' => $email,
                        'password' => Hash::make($email . $this->token),
                        'email_verified_at' => Carbon::now(),
                        'set_password' => '0',
                        'photo' => $photo
                    ]);
                } else {
                    if (is_null($user->email_verified_at)) {
                        $user->update([
                            'email_verified_at' => Carbon::now()
                        ]);
                    }
                }
                $accessToken = $user->createToken('authToken')->accessToken;
                return ResponseFormatter::success($accessToken, 200, 200);
            } else {
                return ResponseFormatter::error('Invalid Token! {Server Token : '.$token.'}{Device Token : '.request()->token.'}');
            }
        } else {
            return ResponseFormatter::error('Invalid Provider!', 500, 500);
        }
    }

    public function verify($email)
    {
        VerifyData::create([
            'user_id' => $this->user->id,
            'token' => $this->token
        ]);
        Mail::to($email)->send(new Verify($this->token));
    }
}
