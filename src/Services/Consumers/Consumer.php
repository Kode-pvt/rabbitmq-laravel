<?php

namespace KodePvt\RabbitmqLaravel\Services\Consumers;

use KodePvt\RabbitmqLaravel\Contracts\ShouldAcknowledgeMessages;
use Illuminate\Support\Facades\Log;
use KodePvt\RabbitmqLaravel\Contracts\IsConsumer;
use KodePvt\RabbitmqLaravel\Facades\Connection;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Message\AMQPMessage;

use function Laravel\Prompts\info;

abstract class Consumer implements IsConsumer
{
    protected bool $defaultNoAck = true;

    public function handler(AMQPMessage $message): void
    {
        $this->before($message);
        $this->handle($message);
        $this->after($message);
    }

    abstract function handle(AMQPMessage $message): void;

    protected function before(AMQPMessage $message): void
    {
        info("Message received: {$message->getBody()}");
    }

    protected function after(AMQPMessage $message)
    {
        if ($this instanceof ShouldAcknowledgeMessages) {
            $message->ack();
        }
    }

    public function basic_consume(string $queue, string $consumer_tag = '', bool $no_local = false, ?bool $no_ack, bool $exclusive = false, bool $nowait = false): void
    {
        $no_ack = $no_ack ?? $this->defaultNoAck;
        Connection::getChannel()->basic_consume($queue, $consumer_tag, $no_local, $no_ack, $exclusive, $nowait, [$this, 'handler']);
    }
}
