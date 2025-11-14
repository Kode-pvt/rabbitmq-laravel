<?php

namespace KodePvt\RabbitmqLaravel\Console\Commands;

use KodePvt\RabbitmqLaravel\Facades\RabbitMQ;
use Illuminate\Console\Command;

use function Laravel\Prompts\info;

class RPCServe extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rabbit:rpc-serve {class? : Server class name.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Starts an RPC server';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $className = $this->getNamespace($this->argument('class'));
        $server = new $className;
        RabbitMQ::startRPCServer($server);
        // $name = $this->argument('name') ?? config('rabbitmq.queue');
        // RabbitMQ::declareQueue($name, $this->option('durable'));
        // info("Queue declared: {$name}");
    }

    private function getNamespace($className)
    {
        return config('rabbitmq.rpc_namespaces.server') . '\\' . $className;
    }
}
