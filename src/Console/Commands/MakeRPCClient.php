<?php

namespace KodePvt\RabbitmqLaravel\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputArgument;

class MakeRPCClient extends GeneratorCommand
{
    protected $name = 'rabbit:make-rpc-client';
    protected $description = 'Create a new rpc client class';
    protected $type = "RabbitMQ RPC Client";

    protected function getStub()
    {
        return __DIR__ . '/stubs/rpc/client.stub';
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        $customNamespace = config('rabbitmq.rpc_namespaces.client', 'App\RabbitMQ\RPC\Clients');

        if ($customNamespace !== 'App\RabbitMQ\RPC\Clients') {
            return $customNamespace;
        }

        return $rootNamespace . '\RabbitMQ\RPC\Clients';
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
            ['name', InputArgument::REQUIRED, 'The name of the rpc client class']
        ];
    }

    protected function getOptions()
    {
        return [];
    }

    protected function afterHandle()
    {
        $className = $this->qualifyClass($this->getNameInput());
        $clientName = class_basename($className);

        $this->line('');
        $this->components->info("RabbitMQ rpc client [{$clientName}] created successfully.");
    }

    protected function displayUsageInstructions($clientName)
    {
        $this->line('');
        $this->components->twoColumnDetail('Usage:', "php artisan rabbit:rpc-call");
    }
}
