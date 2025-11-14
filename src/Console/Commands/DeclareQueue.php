<?php

namespace KodePvt\RabbitmqLaravel\Console\Commands;

use KodePvt\RabbitmqLaravel\Facades\RabbitMQ;
use Illuminate\Console\Command;

use function Laravel\Prompts\info;

class DeclareQueue extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rabbit:declare-queue {name? : Name for the queue. If not provided default from env will be used} {--durable : Makes queue persistent}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Adds a queue if not exists';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->argument('name') ?? config('rabbitmq.queue');
        RabbitMQ::declareQueue($name, $this->option('durable'));
        info("Queue declared: {$name}");
    }
}
