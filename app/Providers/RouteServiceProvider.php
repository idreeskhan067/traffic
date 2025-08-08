<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * @var string
     */
    public const HOME = '/dashboard';

    /**
     * Define your route model bindings, pattern filters, etc.
     */
public function boot()
{
    $this->routes(function () {
        Route::prefix('api')
            ->middleware('api')
            ->group(base_path('routes/api.php'));

        Route::middleware('web')
            ->group(base_path('routes/web.php'));
    });
}

    /**
     * Redirect based on user role after login.
     */
    public static function redirectToBasedOnRole(): string
    {
        $user = auth()->user();

        if ($user && $user->hasRole('admin')) {
            return '/dashboard';
        } elseif ($user && $user->hasRole('warden')) {
            return '/employee/dashboard';
        }

        return '/';
    }
}
