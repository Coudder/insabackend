<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Helpers\JwtAuth; ///cargamos el helper
use App\Helpers\JwtAuthu;

class JwtAuthServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        require_once app_path().'/Helpers/JwtAuth.php';
       /* require_once app_path().'/Helpers/*.php';*/
       // require_once ROOT . '/Helpers/*.php';
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
