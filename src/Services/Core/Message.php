<?php

namespace KodePvt\RabbitmqLaravel\Services\Core;

use KodePvt\RabbitmqLaravel\Contracts\Services\MessageServiceContract;
use PhpAmqpLib\Message\AMQPMessage;

class Message implements MessageServiceContract
{
    public function __construct(protected mixed $message, protected bool $persistent = false, protected ?string $corr_id = null, protected ?string $callback_queue = null) {}

    public function make(): AMQPMessage
    {
        $properties = $this->getPropertiesArray();

        return new AMQPMessage($this->message, $properties);
    }

    protected function getPropertiesArray(): array
    {
        $arr = [
            'delivery_mode' => $this->persistent ? AMQPMessage::DELIVERY_MODE_PERSISTENT : AMQPMessage::DELIVERY_MODE_NON_PERSISTENT
        ];

        if ($this->corr_id != null) {
            $arr['correlation_id'] = $this->corr_id;
        }

        if ($this->callback_queue != null) {
            $arr['reply_to'] = $this->callback_queue;
        }

        return $arr;
    }

    public function setPersistence(bool $persistent): void
    {
        $this->persistent = $persistent;
    }

    public function setCorrelationId(string $id): void
    {
        $this->corr_id = $id;
    }
}
