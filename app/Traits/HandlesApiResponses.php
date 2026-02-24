<?php

namespace App\Traits;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\Client\Response;

/**
 * Trait for handling API responses, especially authentication errors
 */
trait HandlesApiResponses
{
    /**
     * Check if API response indicates an expired/invalid token
     *
     * @param Response $response
     * @return bool
     */
    protected function isTokenExpired(Response $response): bool
    {
        return $response->status() === 401;
    }

    /**
     * Handle API response and redirect to login if token expired
     *
     * @param Response $response
     * @param string $context Optional context for logging
     * @return \Illuminate\Http\RedirectResponse|null
     */
    protected function handleApiResponse(Response $response, string $context = 'API call')
    {
        if ($this->isTokenExpired($response)) {
            Log::warning("Session expired during {$context}", [
                'status' => $response->status(),
            ]);

            // Clear the session
            session()->flush();

            // Check if this is an AJAX request
            if (request()->ajax() || request()->wantsJson()) {
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

        return null;
    }

    /**
     * Create a session expired redirect response
     *
     * @param string $message Custom message (optional)
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function sessionExpiredRedirect(string $message = 'Your session has expired. Please log in again.')
    {
        session()->flush();

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => false,
                'message' => $message,
                'redirect' => route('login'),
                'session_expired' => true
            ], 401);
        }

        return redirect()->route('login')->with('toast_warning', $message);
    }
}
