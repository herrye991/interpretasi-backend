<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Helpers\ResponseFormatter;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function __construct() {
        $this->token = base64_encode(Carbon::now()->format('Y-m-d H:i:s'));
    }

    public function signup(Request $request)
    {
        $validatedData = $request->validate([
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
        return ResponseFormatter::success('Please confirm your email.', 403);
    }

    public function signin(Request $request)
    {

    }

    public function signout(Request $request)
    {
        $request->user('api')->token()->revoke();
        return ResponseFormatter::success('Token revoked', 200);
    }

    public function oauth($provider)
    {
        // return $this->token . ' | ' . request()->token;
        $email = request()->email;
        $display_name = request()->displayName;
        $user = User::where('email', $email)->first();
        if ($provider == 'google') {
            // if ($this->token == request()->token) {
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
                        'provider' => 'google',
                        'email_verified_at' => Carbon::now(),
                        'set_password' => '0'
                    ]);
                }
                $accessToken = $user->createToken('authToken')->accessToken;
                return ResponseFormatter::success($accessToken, 200, 200);
            // }
            // return ResponseFormatter::error('Invalid token!', 403, 403);
        } else {
            return ResponseFormatter::error('Error Provider Parrameters', 500, 500);
        }
    }
}
