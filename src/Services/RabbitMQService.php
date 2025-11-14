<?php

namespace KodePvt\RabbitmqLaravel\Services;

use KodePvt\RabbitmqLaravel\Services\Consumers\Consumer;
use Illuminate\Support\Facades\Log;
use KodePvt\RabbitmqLaravel\Contracts\Connections\ConnectionContract;
use KodePvt\RabbitmqLaravel\Contracts\IsConsumer;
use KodePvt\RabbitmqLaravel\Contracts\Services\ExchangeServiceContract;
use KodePvt\RabbitmqLaravel\Contracts\Services\QueueServiceContract;
use KodePvt\RabbitmqLaravel\Enums\ExchangeType;
use KodePvt\RabbitmqLaravel\Facades\Connection;
use KodePvt\RabbitmqLaravel\Factories\PublisherFactory;
use KodePvt\RabbitmqLaravel\RPC\Server;
use KodePvt\RabbitmqLaravel\Services\Core\Message;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

use function Laravel\Prompts\info;

class RabbitMQService
{
    // protected bool $defaultAck = false;

    public function __construct(
        protected ExchangeServiceContract $exchange,
        protected readonly ConnectionContract $connection,
        protected QueueServiceContract $queue,
        protected PublisherFactory $publisherFactory
    ) {}

    public function declareQueue(string $queueName, bool $durable = false): ?array
    {
        return $this->queue->declare($queueName, $durable);
    }

    public function declareExchange(string $exchangeName, ExchangeType $exchangeType): ?array
    {
        return $this->exchange->declare($exchangeName, $exchangeType);
    }

    public function basicPublish(Message $message, string $destination = '', bool $persistent = false, string $type, string $routingKey = ''): void
    {
        $publisher = $this->publisherFactory->make($type);
        $publisher->basic_publish($message, $destination, $persistent, $routingKey);
    }

    public function queueBind(string $queue, string $exchange, string|array $routingKey = ''): void
    {
        $this->queue->bind($queue, $exchange, $routingKey);
    }

    public function basicConsume(string $queue, Consumer $consumer, string $consumer_tag, bool $exclusive, bool $no_local, bool $nowait, ?bool $no_ack): void
    {
        $consumer->basic_consume($queue, $consumer_tag, $no_local, $no_ack, $exclusive, $nowait);
    }

    public function startRPCServer(Server $server, array $topics = []): void
    {
        $this->qos(0, 1, false);

        $rpc_topics = !empty($topics) ? $topics : config('rabbitmq.rpc_topics');

        if (! empty($rpc_topics)) {
            $this->queueBind($server->queue, $server->exchange, $rpc_topics);
        }

        Connection::getChannel()->basic_consume($server->queue, '', false, false, false, false, [$server, 'handler']);

        try {
            Connection::getChannel()->consume();
        } catch (\Throwable $exception) {
            info($exception->getMessage());
            Log::error($exception->getMessage());
        }
    }

    public function qos($prefetch_size = null, $prefetch_count = 1, $a_global = false)
    {
        $this->connection->getChannel()->basic_qos($prefetch_size, $prefetch_count, $a_global);
    }

    public function consume(): void
    {
        try {
            $this->connection->getChannel()->consume();
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
        }
    }
}
