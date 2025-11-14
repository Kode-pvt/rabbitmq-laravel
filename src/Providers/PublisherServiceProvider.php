<?php

namespace KodePvt\RabbitmqLaravel\Providers;

use Illuminate\Support\ServiceProvider;
use KodePvt\RabbitmqLaravel\Factories\PublisherFactory;

class PublisherServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('rabbit-publisher-factory', function () {
            return new PublisherFactory;
        });
    }

    public function boot()
    {
        //
    }
}
