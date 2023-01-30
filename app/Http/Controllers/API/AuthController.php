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
use App\Helpers\Curl;

class AuthController extends Controller
{
    public function __construct() {
        $this->now = Carbon::now();
        $this->token = bin2hex(base64_encode($this->now));
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
        VerifyData::create([
            'user_id' => $user->id,
            'token' => $this->token
        ]);
        $details = [
            'title' => 'Interpretasi ID',
            'body' => [
                'Terima kasih telah mendaftar di Interpretasi ID! Kamu harus membuka tautan ini dalam 1 hari sejak pendaftaran untuk mengaktifkan akun.',
                'https://interpretasi.id/account/accept/'.$this->now->format('Y-m-d').'/'.$this->token,
                'Bersenang-senang, dan jangan ragu untuk menghubungi kami dengan umpan balik Anda.'
            ],
        ];
        Mail::to($request->email)->send(new Verify($details));
        return ResponseFormatter::success('Please confirm your email.', 403);
    }

    public function signin(Request $request)
    {
        
    }

    public function signout(Request $request)
    {
        request()->header("Accept", "application/json");
        $request->user('api')->token()->revoke();
        return ResponseFormatter::success('Token revoked', 200);
    }

    public function oauth($provider)
    {
        $curl = Curl::get('https://worldtimeapi.org/api/timezone/Asia/Jakarta', []);
        $token = base64_encode(Carbon::createFromTimestamp($curl['unixtime'])->toDateTimeString());;
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
                }
                $accessToken = $user->createToken('authToken')->accessToken;
                return ResponseFormatter::success($accessToken, 200, 200);
            } else {
                return ResponseFormatter::error('Invalid Token! ' . '(' . $token . ')' . '(' . request()->token . ')', 500, 500);
            }
        } else {
            return ResponseFormatter::error('Invalid Provider!', 500, 500);
        }
    }
}
