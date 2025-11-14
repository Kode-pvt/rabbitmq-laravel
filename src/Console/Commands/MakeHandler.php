<?php

namespace KodePvt\RabbitmqLaravel\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputArgument;

class MakeHandler extends GeneratorCommand
{
    protected $name = 'rabbit:make-handler';
    protected $description = 'Create a new rpc handler';
    protected $type = "RabbitMQ RPC Handler";

    protected function getStub()
    {
        return __DIR__ . '/stubs/handlers/handler.stub';
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        $customNamespace = config('rabbitmq.handler_namespaces', 'App\RabbitMQ\RPC\Handlers');

        if ($customNamespace !== 'App\RabbitMQ\RPC\Handlers') {
            return $customNamespace;
        }

        return $rootNamespace . '\RabbitMQ\RPC\Handlers';
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
        $this->components->info("RabbitMQ rpc handler [{$clientName}] created successfully.");
    }

    protected function displayUsageInstructions($clientName)
    {
        $this->line('');
    }
}
