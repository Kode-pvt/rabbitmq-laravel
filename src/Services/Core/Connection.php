<?php

namespace KodePvt\RabbitmqLaravel\Services\Core;

use KodePvt\RabbitmqLaravel\Contracts\Connections\ConnectionContract;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;

use function Laravel\Prompts\info;

class Connection implements ConnectionContract
{
    protected AMQPStreamConnection $connection;
    protected AMQPChannel $channel;

    public function __construct(string $host, int $port, string $user, string $pass, string $vhost = '/')
    {
        $this->connection = new AMQPStreamConnection($host, $port, $user, $pass, $vhost);
    }

    public function getChannel(): AMQPChannel
    {
        if (! isset($this->channel)) {
            $this->channel = $this->connection->channel();
        }
        return $this->channel;
    }

    public static function fromArray(array $config): self
    {
        return new self(
            $config['host'] ?? 'localhost',
            $config['port'] ?? 5672,
            $config['user'] ?? null,
            $config['pass'] ?? null,
            $config['vhost'] ?? "/",
        );
    }

    public function closeConnection(): void
    {
        $this->connection->close();
    }

    public function closeChannel(): void
    {
        $this->channel->close();
    }

    public function __destruct()
    {
        if (isset($this->channel)) $this->closeChannel();

        $this->closeConnection();
    }
}
