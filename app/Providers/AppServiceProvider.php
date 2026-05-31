<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Pagination\Paginator;
use Carbon\Carbon;

use App\Listeners\HandleTesterWorkflow;

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
        Event::subscribe(HandleTesterWorkflow::class);
        View::composer('*', function ($view) {
            $view->with('user', Auth::user());
        });
        Paginator::useBootstrapFive();
        Carbon::setLocale('id');
    }
}
