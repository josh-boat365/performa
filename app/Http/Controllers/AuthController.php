<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use App\Services\AppraisalApiService;
use App\Exceptions\ApiException;

class AuthController extends Controller
{
    /**
     * @var AppraisalApiService
     */
    protected AppraisalApiService $appraisalService;

    /**
     * Create a new controller instance
     */
    public function __construct(AppraisalApiService $appraisalService)
    {
        $this->appraisalService = $appraisalService;
    }

    /**
     * Handle user login request
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request)
    {
        // Define rate limit key and limit threshold
        $throttleKey = 'login:' . $request->ip();
        $maxAttempts = 5; // Allow up to 5 attempts
        $decayMinutes = 1; // Lockout for 1 minute after max attempts

        // Check rate limit
        if (RateLimiter::tooManyAttempts($throttleKey, $maxAttempts)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()->with('toast_error', "Too many login attempts. Please try again in $seconds seconds.");
        }

        // Validate request input
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        try {
            // Authenticate user via API
            $response = $this->appraisalService->login(
                'Appraisal',
                $request->input('username'),
                $request->input('password'),
                true
            );

            // Check if response contains access token
            if (!isset($response['access_token'])) {
                RateLimiter::hit($throttleKey, $decayMinutes * 60);
                Log::warning('Authentication failed - no access token', ['user' => $request->input('username')]);
                return redirect()->back()->with('toast_error', 'Invalid credentials. Please try again.');
            }

            // Store access token and user profile data in the session
            session([
                'api_token' => $response['access_token'],
                'user_name' => $response['profile']['fullName'] ?? '',
                'user_email' => $response['profile']['email'] ?? '',
                'employee_id' => $response['profile']['id'] ?? '',
                'empRole' => 4,
            ]);

            // Clear rate limit on success
            RateLimiter::clear($throttleKey);

            return redirect()->route('dashboard.index')->with('toast_success', 'Logged in successfully');
        } catch (ApiException $e) {
            // Increment the rate limit on exception
            RateLimiter::hit($throttleKey, $decayMinutes * 60);

            // Log the error
            Log::warning('API authentication error', [
                'user' => $request->input('username'),
                'status' => $e->getStatusCode(),
                'message' => $e->getMessage(),
            ]);

            // Return user-friendly error message
            if ($e->isConnectionError()) {
                return redirect()->back()->with('toast_error', 'Unable to connect to the server. Please check your internet connection.');
            }

            return redirect()->back()->with('toast_error', 'Invalid credentials. Please try again.');
        } catch (\Exception $e) {
            // Increment the rate limit on exception
            RateLimiter::hit($throttleKey, $decayMinutes * 60);

            // Log unexpected errors
            Log::error('Unexpected error during authentication', [
                'user' => $request->input('username'),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->with('toast_error', 'An error occurred. Please try again later.');
        }
    }



    // public function getAuthToken(Request $request)
    // {
    //     try {

    //         // Validate the query string parameters
    //         $validatedData = $request->validate([
    //             'access_token' => 'required|string',
    //             'fullName'     => 'required|string',
    //             'email'        => 'required|email',
    //             'id'           => 'required',
    //         ]);

    //         // Prepare user data from the validated query data
    //         $userData = [
    //             'api_token'   => $validatedData['access_token'],
    //             'user_name'   => $validatedData['fullName'],
    //             'user_email'  => $validatedData['email'],
    //             'employee_id' => $validatedData['id'],
    //         ];

    //         // Additional logic like user session creation or updating records can go here
    //         session([
    //             'api_token'   => $userData['api_token'],
    //             'user_name'   => $userData['user_name'],
    //             'user_email'  => $userData['user_email'],
    //             'employee_id' => $userData['employee_id'],
    //         ]);
    //         // Return a JSON response back to the requester
    //         return response()->json([
    //             'message' => 'User authenticated successfully',
    //             'data'    => $userData,
    //         ], 200);
    //     } catch (ValidationException $e) {
    //         // Catch and return validation errors in a structured format
    //         return response()->json([
    //             'error'   => 'Validation failed',
    //             'details' => $e->errors(),
    //         ], 422);
    //     } catch (\Exception $e) {
    //         // Catch any other exceptions and return an error message
    //         return response()->json([
    //             'error'   => 'An error occurred',
    //             'details' => $e->getMessage(),
    //         ], 500);
    //     }
    // }


    public function getAuthToken(Request $request)
    {
        try {

            // dd($params);
            // Decode the URL-encoded parameters

            // Validate the query string parameters
            $validatedData = $request->validate([
                'access_token' => 'required|string',
                'fullName'     => 'required|string',
                'email'        => 'required|email',
                'id'           => 'required',
            ]);

            // Prepare user data from the validated query data
            $userData = [
                'api_token'   => $validatedData['access_token'],
                'user_name'   => $validatedData['fullName'],
                'user_email'  => $validatedData['email'],
                'employee_id' => $validatedData['id'],
            ];

            // Additional logic like user session creation or updating records can go here
            session([
                'api_token'   => $userData['api_token'],
                'user_name'   => $userData['user_name'],
                'user_email'  => $userData['user_email'],
                'employee_id' => $userData['employee_id'],
                'empRole' => 4,
            ]);

            return redirect()->route('dashboard.index')->with('toast_success', 'Logged in successfully');
            // Return a JSON response back to the requester
            // return response()->json([
            //     'message' => 'User authenticated successfully',
            //     'data'    => $userData,
            // ], 200);
        } catch (ValidationException $e) {
            // Catch and return validation errors in a structured format
            return response()->json([
                'error'   => 'Validation failed',
                'details' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            // Catch any other exceptions and return an error message
            return response()->json([
                'error'   => 'An error occurred',
                'details' => $e->getMessage(),
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        // Clear all session data related to authentication
        $request->session()->forget(['api_token', 'user_name', 'user_email']);

        // Clear all session data
        $request->session()->flush();

        // Redirect to the external URL with proper formatting
        return redirect()->away('http://192.168.1.200:5125/');
    }
}
