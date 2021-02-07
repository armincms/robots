<?php

namespace Armincms\Robots;
  
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider as Provider; 
use Laravel\Nova\Nova as LaravelNova;

class ServiceProvider extends Provider implements DeferrableProvider
{      
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    { 
        LaravelNova::resources([
            Nova\Robots::class,
        ]);
    } 

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }

    /**
     * Get the events that trigger this service provider to register.
     *
     * @return array
     */
    public function when()
    {
        return [
            \Laravel\Nova\Events\ServingNova::class,
        ];
    }
}
