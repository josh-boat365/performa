<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Gate;
use Opcodes\LogViewer\Facades\LogViewer;
use Symfony\Component\HttpFoundation\Response;

class LogViewerMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (
            config('log-viewer.require_auth_in_production', false)
            && App::isProduction()
            && ! Gate::has('viewLogViewer')
            && ! LogViewer::hasAuthCallback()
        ) {
            abort(403);
        }



        LogViewer::auth(function ($request) {
            return $request->user()
                && in_array($request->user()->email, [
                    'jnboateng@bestpointgh.com',
                    'cashun@bestpointgh.com',
                    'ranane@bestpointgh.com',
                    'dkwarteng@bestpointgh.com',
                    'ekwakye@bestpointgh.com',
                ]);
        });

        return $next($request);

    }
}
