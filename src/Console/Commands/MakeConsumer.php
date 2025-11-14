<?php

namespace KodePvt\RabbitmqLaravel\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputArgument;

class MakeConsumer extends GeneratorCommand
{
    protected $name = 'rabbit:make-consumer';
    protected $description = 'Create a new consumer class';
    protected $type = "RabbitMQ Consumer";

    protected function getStub()
    {
        return __DIR__ . '/stubs/consumer.stub';
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        $customNamespace = config('rabbitmq.consumer_namespace', 'App\RabbitMQ\Consumers');

        if ($customNamespace !== 'App\RabbitMQ\Consumers') {
            return $customNamespace;
        }

        return $rootNamespace . '\RabbitMQ\Consumers';
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
            ['name', InputArgument::REQUIRED, 'The name of the consumer class']
        ];
    }

    protected function getOptions()
    {
        return [];
    }

    protected function afterHandle()
    {
        $className = $this->qualifyClass($this->getNameInput());
        $consumerName = class_basename($className);

        $this->line('');
        $this->components->info("RabbitMQ consumer [{$consumerName}] created successfully.");
    }

    protected function displayUsageInstructions($consumerName)
    {
        $this->line('');
        $this->components->twoColumnDetail('Usage:', "php artisan rabbit:consume");
    }
}
