<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SessionNotFound
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $apiToken = session('api_token');
        $tokenIssuedAt = session('token_issued_at');

        // Check if the token is present and if it has expired
        if (empty($apiToken) || (cache('token_expiration_' . $apiToken) ?? (time() - $tokenIssuedAt) > 3600)) {
            Auth::logout();
            $request->session()->flush();
            return redirect()->route('login')->with('toast_error', 'Your session has expired. Please log in again.');
        }

        return $next($request);
    }
}
