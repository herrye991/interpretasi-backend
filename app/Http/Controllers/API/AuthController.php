<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Helpers\ResponseFormatter;

class AuthController extends Controller
{
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
        
    }
}
