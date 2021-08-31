<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
      $app_root = base_path();
      $this->loadViewsFrom($app_root . '/resources/views/page', 'theme');
      $this->loadViewsFrom(glob($app_root.'/app/Rapyd/Modules/**/Views/Public'), 'rapyd_module_public');
    }
}
