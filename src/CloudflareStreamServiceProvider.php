<?php

namespace fronji\fronjicfstream;

use Illuminate\Support\ServiceProvider;

class fronjicfstreamServiceProvider extends ServiceProvider
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
