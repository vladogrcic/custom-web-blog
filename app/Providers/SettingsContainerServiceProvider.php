<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class SettingsContainerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        require_once app_path() . '/Containers/SettingsContainer.php';

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
