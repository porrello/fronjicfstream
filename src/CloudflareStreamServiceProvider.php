<?php

namespace fronji\fronjiCfStream;

use Illuminate\Support\ServiceProvider;

class fronjiCfStreamServiceProvider extends ServiceProvider
{
    /**
     * Boot.
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/config/cloudflare-stream.php' => config_path('cloudflare-stream.php'),
        ]);
    }

    /**
     * Register.
     */
    public function register()
    {
        //
    }
}
