<?php

namespace KodePvt\RabbitmqLaravel\RPC;

use KodePvt\RabbitmqLaravel\Contracts\IsConsumer;
use KodePvt\RabbitmqLaravel\Facades\Connection;
use KodePvt\RabbitmqLaravel\Facades\PublisherFactory;
use KodePvt\RabbitmqLaravel\Facades\Queue;
use KodePvt\RabbitmqLaravel\Helpers\Helpers;
use KodePvt\RabbitmqLaravel\Services\Core\Message;
use PhpAmqpLib\Message\AMQPMessage;

use function Laravel\Prompts\info;
use function Laravel\Prompts\spin;

abstract class Client implements IsConsumer
{
    private $callback_queue;
    protected $response;
    private $corr_id;
    private $channel;
    private $queue;

    protected $queueName;

    public function __construct()
    {
        $this->channel = Connection::getChannel();
        list($this->queue,,) = Queue::declare(config('rpc_queue', ""));
        list($this->callback_queue,,) = Queue::declare("", false, false, true, false);
        $this->basic_consume();

        $this->closeQueues();
    }

    private function closeQueues()
    {
        Helpers::queueDeleteOnExit($this->queue);
        Helpers::queueDeleteOnExit($this->callback_queue);
    }

    private function basic_consume(): void
    {
        $this->channel->basic_consume($this->callback_queue, '', false, true, false, false, [$this, 'handler']);
    }

    public function handler(AMQPMessage $req): void
    {
        if ($req->get('correlation_id') === $this->corr_id) {
            $this->response = $req->getBody();
        }
    }

    public function call()
    {
        $this->response = null;
        $this->corr_id = uniqid();

        $publisher = PublisherFactory::make('exchange');
        $messageProperties = $this->getMessageProperties();

        $message = new Message($this->payload(), false, $messageProperties['correlation_id'], $messageProperties['reply_to']);
        $publisher->basic_publish($message, config('rabbitmq.rpc_exchange'), false, $this->route());
        info("request sent to the server.");

        spin(function () {
            while (!$this->response) {
                $this->channel->wait();
            }
        }, "Waiting for the response");

        return $this->handle($this->response);
    }

    private function payload()
    {
        return json_encode([
            'route' => $this->route(),
            'payload' => $this->data()
        ]);
    }

    private function getMessageProperties()
    {
        return [
            'correlation_id' => $this->corr_id,
            'reply_to' => $this->callback_queue,
        ];
    }
    abstract public function handle($response);
    abstract protected function route(): string;
    abstract protected function data(): string;

    public function __destruct()
    {
        $this->closeQueues();
    }
}
