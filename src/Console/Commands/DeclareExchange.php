<?php

namespace KodePvt\RabbitmqLaravel\Console\Commands;

use Illuminate\Console\Command;
use KodePvt\RabbitmqLaravel\Enums\ExchangeType;
use KodePvt\RabbitmqLaravel\Facades\RabbitMQ;

use function Laravel\Prompts\info;

class DeclareExchange extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rabbit:declare-exchange {name : Name for the exchange. If not provided default from env will be used}
                            {--type= : Type can be fanout, topic, headers, and direct}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Adds an exchange if not exists';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        RabbitMQ::declareExchange($this->argument('name'), ExchangeType::tryFrom($this->option('type') ?? 'fanout'));
        info("Exchange declared: {$this->argument('name')}");
    }
}
