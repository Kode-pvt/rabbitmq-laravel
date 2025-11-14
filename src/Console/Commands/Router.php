<?php

namespace KodePvt\RabbitmqLaravel\Console\Commands;

use Illuminate\Console\Command;
use KodePvt\RabbitmqLaravel\Facades\RabbitMQ;
use KodePvt\RabbitmqLaravel\RPC\Router as RPCRouter;

class Router extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rabbit:router
                            {--topics= : Topic to subscribe by this router}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Starts the router';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $topics = [];
        if ($this->option('topics')) {
            $topics = explode(',', str_replace(" ", '', $this->option('topics')));
        }
        RabbitMQ::startRPCServer(new RPCRouter, $topics);
    }
}
