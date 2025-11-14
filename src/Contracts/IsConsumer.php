<?php

namespace KodePvt\RabbitmqLaravel\Contracts;

use PhpAmqpLib\Message\AMQPMessage;

interface IsConsumer
{
    public function handler(AMQPMessage $message): void;
}
