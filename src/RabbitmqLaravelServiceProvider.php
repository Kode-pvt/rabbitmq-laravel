<?php

namespace KodePvt\RabbitmqLaravel;

use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;
use KodePvt\RabbitmqLaravel\Console\Commands\AddExchange;
use KodePvt\RabbitmqLaravel\Console\Commands\AddQueue;
use KodePvt\RabbitmqLaravel\Console\Commands\ConsumeMessages;
use KodePvt\RabbitmqLaravel\Console\Commands\DeclareExchange;
use KodePvt\RabbitmqLaravel\Console\Commands\DeclareQueue;
use KodePvt\RabbitmqLaravel\Console\Commands\MakeConsumer;
use KodePvt\RabbitmqLaravel\Console\Commands\MakeHandler;
use KodePvt\RabbitmqLaravel\Console\Commands\MakeRPCClient;
use KodePvt\RabbitmqLaravel\Console\Commands\MakeRPCServer;
use KodePvt\RabbitmqLaravel\Console\Commands\PublishMessage;
use KodePvt\RabbitmqLaravel\Console\Commands\QueueBind;
use KodePvt\RabbitmqLaravel\Console\Commands\Router as CommandsRouter;
use KodePvt\RabbitmqLaravel\Console\Commands\RPCCall;
use KodePvt\RabbitmqLaravel\Console\Commands\RPCInstall;
use KodePvt\RabbitmqLaravel\Console\Commands\RPCServe;
use KodePvt\RabbitmqLaravel\Contracts\Connections\ConnectionContract;
use KodePvt\RabbitmqLaravel\Contracts\Services\ExchangeServiceContract;
use KodePvt\RabbitmqLaravel\Contracts\Services\QueueServiceContract;
use KodePvt\RabbitmqLaravel\Services\Core\Connection as CoreConnection;
use KodePvt\RabbitmqLaravel\Facades\Connection;
use KodePvt\RabbitmqLaravel\Factories\PublisherFactory;
use KodePvt\RabbitmqLaravel\Providers\PublisherServiceProvider;
use KodePvt\RabbitmqLaravel\Providers\QueueServiceProvider;
use KodePvt\RabbitmqLaravel\RPC\Router;
use KodePvt\RabbitmqLaravel\Services\Exchanges\ExchangeService;
use KodePvt\RabbitmqLaravel\Services\RabbitMQService;

class RabbitmqLaravelServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->register(QueueServiceProvider::class);
        $this->app->register(PublisherServiceProvider::class);

        $this->mergeConfigFrom(
            __DIR__ . '/../config/rabbitmq.php',
            'rabbitmq'
        );

        $this->app->singleton(ConnectionContract::class, function () {
            return CoreConnection::fromArray(config('rabbitmq'));
        });

        $this->app->singleton('rabbit-router', function ($app) {
            $router = new Router;
            $app->terminating(function () use ($router) {
                // nothing
            });
            return $router;
        });

        $this->app->singleton('connection', function () {
            return $this->app->make(ConnectionContract::class);
        });

        $this->app->singleton(ExchangeServiceContract::class, function () {
            return new ExchangeService();
        });

        $this->app->singleton('rabbitmq', function () {
            return new RabbitMQService(
                $this->app->make(ExchangeServiceContract::class),
                $this->app->make(ConnectionContract::class),
                $this->app->make(QueueServiceContract::class),
                $this->app->make(PublisherFactory::class)
            );
        });
    }

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/rabbitmq.php' => config_path('rabbitmq.php')
            ], 'rabbitmq-config');

            $this->publishes([
                __DIR__ . '/../routes/rabbit-rpc.php' => config_path('../routes/rabbit-rpc.php')
            ], 'rabbitmq-routes');
        }

        $this->commands([
            DeclareQueue::class,
            ConsumeMessages::class,
            PublishMessage::class,
            MakeConsumer::class,
            DeclareExchange::class,
            QueueBind::class,
            MakeRPCClient::class,
            MakeRPCServer::class,
            RPCServe::class,
            RPCCall::class,
            CommandsRouter::class,
            MakeHandler::class,
        ]);

        // if (File::exists(base_path('routes/rabbit-rpc.php'))) {
        //     require_once base_path('routes/rabbit-rpc.php');
        // } else {
        //     throw new \Exception("routes file not found. Please run rabbit:rpc-install");
        // }

        // $this->app->afterResolving('rabbit-router', function ($router) {
        //     if (File::exists(base_path('routes/rabbit-rpc.php'))) {
        //         require_once base_path('routes/rabbit-rpc.php');
        //     }
        // });
    }
}
