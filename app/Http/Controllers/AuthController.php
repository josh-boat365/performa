<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\RateLimiter;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        // Define rate limit key and limit threshold
        $throttleKey = 'login:' . $request->ip();
        $maxAttempts = 5; // Allow up to 5 attempts
        $decayMinutes = 1; // Lockout for 1 minute after max attempts

        // Check rate limit
        if (RateLimiter::tooManyAttempts($throttleKey, $maxAttempts)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()->with('toast_error', ['message' => "Too many login attempts. Please try again in $seconds seconds."]);
        }

        // Validate request input
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // Prepare data for the API request
        $data = [
            'appName' => 'HRMS',
            'user' => $request->input('username'),
            'password' => $request->input('password'),
            'validateAppAcess' => true
        ];

        try {
            // Send the POST request to the API
            $response = Http::withoutVerifying()->post('http://bp-ho-gcupdate.Bestpointgh.com:8093/verification/LocalAccount/UserNameOrPhoneOrEmailAndPassword', $data);

            // Check for a successful response and the presence of access token
            if ($response->ok() && isset($response['access_token'])) {
                $data = $response->object();

                // dd($data);

                // Store access token and user profile data in the session
                session([
                    'api_token' => $data->access_token,
                    'user_name' => $data->profile->fullName,
                    'user_email' => $data->profile->email,
                    'lastActivityTime' => time(), // Initialize last activity time
                ]);

                logger('Authentication successful. Session set:', session()->all());

                // Clear rate limit on success
                RateLimiter::clear($throttleKey);

                // alert('Title', 'Lorem Lorem Lorem', 'success');

                return redirect()->route('dashboard.index')->with('toast_success', 'Logged in successfully');
            }
            
            // Increment the rate limit on failed login attempt
            RateLimiter::hit($throttleKey, $decayMinutes * 60);

            // Log the error if authentication fails
            Log::warning('Authentication failed for user', ['user' => $request->input('user')]);
            // Return error if authentication fails
            return back()->with('toast_error', ['message' => 'Invalid credentials. Please try again.']);
        } catch (\Exception $e) {
            // Log the exception details
            Log::error('Error during authentication', [
                'user' => $request->input('user'),
                'error' => $e->getMessage(),
            ]);

            // Increment the rate limit on exception
            RateLimiter::hit($throttleKey, $decayMinutes * 60);

            // Return a generic error message to the user
            return back()->with('toast_error', ['message' => 'An error occurred. Please try again later.']);
        }
    }

    public function logout(Request $request)
    {
        // Clear all session data related to authentication
        $request->session()->forget(['api_token', 'user_name', 'user_email']);

        // Optionally, clear all session data
        $request->session()->flush();

        // Redirect to the login page with a success message
        return redirect()->route('login')->with('toast_success', 'You have been logged out successfully.');
    }
}
