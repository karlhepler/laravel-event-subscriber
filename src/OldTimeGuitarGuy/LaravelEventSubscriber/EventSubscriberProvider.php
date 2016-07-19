<?php

namespace OldTimeGuitarGuy\LaravelEventSubscriber;

use Illuminate\Support\ServiceProvider;

class EventSubscriberProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        // Copy over the config file
        $this->publishes([
            __DIR__.'/config.php' => config_path('event_subscriber.php')
        ]);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        // Register the console command
        $this->commands([
            MakeEventSubscriber::class,
        ]);
    }
}
