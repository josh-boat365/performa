<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Opcodes\LogViewer\Facades\LogViewer;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();

        LogViewer::auth(function ($request) {
            return $request->user()
                && in_array($request->user()->email, [
                    'jnboateng@bestpointgh.com',
                    'cashun@bestpointgh.com',
                    'ranane@bestpointgh.com',
                    'dkwarteng@bestpointgh.com',
                    'ekwakye@bestpointgh.com',
                    'kwame.kay365@gmail.com',
                ]);
        });
    }
}
