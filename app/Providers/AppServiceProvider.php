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
      $app_root         = base_path();
      $resource_blades  = $app_root . '/resources/views';
      $module_blades    = glob($app_root.'/app/Modules/**/Views/Public');
      $admin_blades     = glob($app_root.'/app/Modules/**/Views/Admin');

      $this->loadViewsFrom( $admin_blades     , 'module_admin');
      $this->loadViewsFrom( $resource_blades  , 'theme' );
      $this->loadViewsFrom( $module_blades    , 'module_public' );
    }
}
