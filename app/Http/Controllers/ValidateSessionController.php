<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ValidateSessionController extends Controller
{
    public static function validateSession()
    {
        if (empty(session('api_token'))) {
            session()->flush();
            return redirect()->route('login')->with('toast_warning', 'Session expired, login to access the application');
        }
        return null;
    }
}
