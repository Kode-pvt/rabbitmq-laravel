<?php

namespace KodePvt\RabbitmqLaravel\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static void declareQueue(string $queueName, bool $durable = false)
 * @method static void declareExchange(string $exchangeName, KodePvt\Enums\ExchangeType $exchangeType)
 * @method static void basicPublish(string $message, string $destination = '', bool $persistent = '', string $type, string $routingKey = '')
 * @method static void basicConsume(string $queue, \App\Services\Consumers\Consumer $consumer, string $consumer_tag, bool $exclusive, bool $no_local, bool $nowait, ?bool $no_ack)
 * @method static void startRPCServer(Server $server, array $topics = [])
 * @method static void consume()
 * @method static void qos(int $prefetch_size = null, int $prefetch_count = 1, bool $a_global = false))
 * @method static void queueBind(string $queue, string $exchange, string $routingKey = '')
 */


class RabbitMQ extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'rabbitmq';
    }
}
