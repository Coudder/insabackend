<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class JwtAuthuServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
        require_once app_path().'/Helpers/JwtAuthu.php';
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
