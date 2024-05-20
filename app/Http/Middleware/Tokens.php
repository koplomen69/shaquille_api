<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;
use Illuminate\Support\Facades\Auth;


class Tokens
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = session('api_token');
        if (!$token) {
            return back()->with('error', 'Silahkan Login!');
        }
        $user = User::where('remember_token', '=', $token)->first();
        if ($user) {
            Auth::login($user);
        }
        return $next($request);
    }

}
