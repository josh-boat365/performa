<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware to check if API token exists in session
 * This middleware should be applied to routes that require API authentication
 */
class CheckApiToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if API token exists in session
        if (empty(session('api_token'))) {
            Log::info('API token not found in session - redirecting to login');

            // Clear the session
            session()->flush();

            // Handle AJAX requests differently
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Your session has expired. Please log in again.',
                    'redirect' => route('login'),
                    'session_expired' => true
                ], 401);
            }

            return redirect()->route('login')
                ->with('toast_warning', 'Your session has expired. Please log in again.');
        }

        return $next($request);
    }
}
