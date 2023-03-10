<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;

class EmailVerifyChecker
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $user = auth('api')->user();
        $email_verified_at = '';
        if (!is_null($user)) {
            $email_verified_at = $user->email_verified_at;
        }
        if (is_null($email_verified_at)) {
            return response()->json(['message' => 'Unverified'], 403);
        }
        return $next($request);
    }
}
