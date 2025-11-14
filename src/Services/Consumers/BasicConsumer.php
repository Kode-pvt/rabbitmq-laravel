<?php

namespace KodePvt\RabbitmqLaravel\Services\Consumers;

use Illuminate\Support\Facades\Log;
use PhpAmqpLib\Message\AMQPMessage;

class BasicConsumer extends Consumer
{
    public function handle(AMQPMessage $message): void
    {
        Log::info("msg: {$message->getBody()}");
    }
}
