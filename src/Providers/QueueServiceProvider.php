<?php

namespace KodePvt\RabbitmqLaravel\Providers;

use Illuminate\Support\ServiceProvider;
use KodePvt\RabbitmqLaravel\Contracts\Connections\ConnectionContract;
use KodePvt\RabbitmqLaravel\Contracts\Services\QueueServiceContract;
use KodePvt\RabbitmqLaravel\Facades\Connection;
use KodePvt\RabbitmqLaravel\Services\Queues\QueueService;

class QueueServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(QueueServiceContract::class, function () {
            return new QueueService(app(ConnectionContract::class));
        });

        $this->app->singleton('rabbit-queue', QueueServiceContract::class);
    }

    public function boot()
    {
        //
    }
}
