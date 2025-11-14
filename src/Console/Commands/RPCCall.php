<?php

namespace KodePvt\RabbitmqLaravel\Console\Commands;

use KodePvt\RabbitmqLaravel\Facades\RabbitMQ;
use Illuminate\Console\Command;

use function Laravel\Prompts\info;

class RPCCall extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rabbit:rpc-call {class? : Client class name.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Initiate a call to the server from client';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $className = $this->getNamespace($this->argument('class'));
        $client = new $className;
        $client->call();
    }

    private function getNamespace($className)
    {
        return config('rabbitmq.rpc_namespaces.client') . '\\' . $className;
    }
}
