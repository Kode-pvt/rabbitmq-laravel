<?php

namespace KodePvt\RabbitmqLaravel\Console\Commands;

use KodePvt\RabbitmqLaravel\Facades\RabbitMQ;
use Illuminate\Console\Command;
use KodePvt\RabbitmqLaravel\Services\Core\Message;

use function Laravel\Prompts\info;
use function Laravel\Prompts\spin;

class PublishMessage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rabbit:publish
                            {message : message content to send}
                            {--destination= : name of exchange}
                            {--persistent= : true or false}
                            {--type= : Can be queue or exchange}
                            {--key= : Routing Key, should be a string}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publishes a message on selected queue.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $message = new Message($this->argument('message'));
        $routingKey = $this->option('key') ?? '';
        $destination = $this->option('destination') ?? '';
        $persistent = $this->option('persistent') ?? false;
        $type = $this->option('type') ?? 'queue';
        $name = $destination != '' ? $destination : $routingKey;

        spin(function () use ($message, $routingKey, $destination, $persistent, $type) {
            return RabbitMQ::basicPublish($message, $destination, $persistent, $type, $routingKey);
        }, "Publishing message on {$name}...");

        info("Message published");
    }
}
