<?php

namespace KodePvt\RabbitmqLaravel\Console\Commands;

use KodePvt\RabbitmqLaravel\Facades\RabbitMQ;
use KodePvt\RabbitmqLaravel\Services\Consumers\BasicConsumer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

use function Laravel\Prompts\info;

class ConsumeMessages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rabbit:consume {--class= : Consumer class name} {--qos} {queue : Queue name to listen on. config.rabbitmq.queue will be used if not provided.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Start message consumer';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $className = $this->option('class') ?? 'BasicConsumer';
        $class = $this->getNamespace($className);
        $queueName = $this->argument('queue') ?? config('rabbitmq.queue');
        info("Consumer started for {$queueName}");

        if ($this->option('qos')) {
            RabbitMQ::qos(null, 1);
        }
        // RabbitMQ::basicConsume($queueName, new $class, true);
        RabbitMQ::basicConsume($queueName, new $class, '', false, false, false, true);
        RabbitMQ::consume();
    }

    private function getNamespace($className)
    {
        if (class_exists(config('rabbitmq.consumer_namespace') . "\\$className")) {
            return config('rabbitmq.consumer_namespace') . "\\$className";
        }
        return "KodePvt\\RabbitmqLaravel\\Services\\Consumers\\" . $className;
    }
}
