<?php

namespace KodePvt\RabbitmqLaravel\Contracts\Services;

use PhpAmqpLib\Message\AMQPMessage;

interface MessageServiceContract
{
    public function make(): AMQPMessage;
}
