<?php

namespace Dnsimmons\OpenWeather;

use Illuminate\Support\ServiceProvider;

class OpenWeatherServiceProvider extends ServiceProvider{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(){
        $this->publishes([
            __DIR__ . '/config/openweather.php' => config_path('openweather.php'),
        ]);
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register(){

    }

}

