<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SessionTimeout
{
    protected $timeout = 3600; // 60 minutes in seconds

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        $apiToken = session('api_token');


        // Ensure the timeout logic is correct
        $lastActivity = session('lastActivityTime', time());
        $currentTime = time();

        if (($currentTime - $lastActivity) > $this->timeout) {
            logger('Session timeout reached. Logging out.');
            Auth::logout();
            $request->session()->flush();
            return redirect()->route('login')->withErrors(['message' => 'Your session has expired due to inactivity.']);
        }

        if (!$apiToken) {
            Auth::logout();
            $request->session()->flush();
            return redirect()->route('login')->withErrors(['message' => 'Session expired. Please log in again.']);
        }

        // Update last activity
        session(['lastActivityTime' => $currentTime]);


        return $next($request);
    }


}
