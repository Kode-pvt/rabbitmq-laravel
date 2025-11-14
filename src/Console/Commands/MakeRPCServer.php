<?php

namespace KodePvt\RabbitmqLaravel\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputArgument;

class MakeRPCServer extends GeneratorCommand
{
    protected $name = 'rabbit:make-rpc-server';
    protected $description = 'Create a new rpc server class';
    protected $type = "RabbitMQ RPC Server";

    protected function getStub()
    {
        return __DIR__ . '/stubs/rpc/server.stub';
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        $customNamespace = config('rabbitmq.rpc_namespaces.server', 'App\RabbitMQ\RPC\Servers');

        if ($customNamespace !== 'App\RabbitMQ\RPC\Server') {
            return $customNamespace;
        }

        return $rootNamespace . '\RabbitMQ\RPC\Server';
    }

    protected function buildClass($name)
    {
        $stub = parent::buildClass($name);

        $replace = [];

        return str_replace(
            array_keys($replace),
            array_values($replace),
            $stub
        );
    }

    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the rpc server class']
        ];
    }

    protected function getOptions()
    {
        return [];
    }

    protected function afterHandle()
    {
        $className = $this->qualifyClass($this->getNameInput());
        $serverName = class_basename($className);

        $this->line('');
        $this->components->info("RabbitMQ rpc server [{$serverName}] created successfully.");
    }

    protected function displayUsageInstructions($serverName)
    {
        $this->line('');
        $this->components->twoColumnDetail('Usage:', "php artisan rabbit:rpc-serve");
    }
}
