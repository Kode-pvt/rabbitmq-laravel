<?php

namespace KodePvt\RabbitmqLaravel\Console\Commands;

use Illuminate\Console\Command;
use KodePvt\RabbitmqLaravel\Facades\RabbitMQ;

use function Laravel\Prompts\info;

class QueueBind extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rabbit:queue-bind
                            {queue : queue name}
                            {exchange : exchange name}
                            {--keys= : routing key. Can be "," separated. When using topic exchange. The key should be a list of words separated by \'.\' i.e. hello.world or quick.brown.fox. You can use * for exactly one word *.brown.fox or *.brown.*. You can use # for zero or more words i.e. quick.# or #.fox}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Bind a queue with an exchange';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $keys = explode(",", $this->option('keys')) ?? [''];
        $queue = $this->argument('queue');
        $exchange = $this->argument('exchange');
        foreach ($keys as $key) {
            RabbitMQ::queueBind($queue, $exchange, $key);
            info("Binding successful: Q:{$queue} + X:{$exchange}");
        }
    }
}
