<?php

namespace KodePvt\RabbitmqLaravel\Helpers;

use KodePvt\RabbitmqLaravel\Facades\Connection;
use Throwable;

class Helpers
{
    public static function queueDeleteOnExit(string $queue)
    {
        pcntl_async_signals(true);
        pcntl_signal(SIGINT, function () use ($queue) {
            echo "\nCaught SIGINT, shutting down $queue...\n";
            // Do cleanup, e.g. delete queues, close connections, etc.
            Connection::getChannel()->queue_delete($queue);
            exit(0);
        });

        register_shutdown_function(function () use ($queue) {
            self::deleteQueue($queue);
        });
    }

    private static function deleteQueue(string $queue): void
    {
        try {
            $channel = Connection::getChannel();
            $channel->queue_delete($queue);
            echo "Queue deleted: {$queue}\n";
        } catch (Throwable $e) {
            // Optionally log the issue, but don't crash
            echo "Failed to delete queue {$queue}: {$e->getMessage()}\n";
        }
    }
}
