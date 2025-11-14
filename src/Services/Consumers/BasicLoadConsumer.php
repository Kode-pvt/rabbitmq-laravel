<?php

namespace KodePvt\RabbitmqLaravel\Services\Consumers;

use KodePvt\RabbitmqLaravel\Contracts\ShouldAcknowledgeMessages;
use Illuminate\Support\Facades\Log;
use PhpAmqpLib\Message\AMQPMessage;

class BasicLoadConsumer extends Consumer implements ShouldAcknowledgeMessages
{
    public function handle(AMQPMessage $message): void
    {
        Log::info("msg: {$message->getBody()}");
        sleep(substr_count($message->getBody(), '.'));
    }
}
