<?php

namespace KodePvt\RabbitmqLaravel\RPC;

use Illuminate\Support\Facades\Artisan;
use KodePvt\RabbitmqLaravel\Contracts\IsConsumer;
use KodePvt\RabbitmqLaravel\Contracts\PublisherContract;
use KodePvt\RabbitmqLaravel\Enums\ExchangeType;
use KodePvt\RabbitmqLaravel\Facades\Connection;
use KodePvt\RabbitmqLaravel\Facades\PublisherFactory;
use KodePvt\RabbitmqLaravel\Facades\RabbitMQ;
use KodePvt\RabbitmqLaravel\Helpers\Helpers;
use KodePvt\RabbitmqLaravel\Services\Core\Message;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Message\AMQPMessage;

use function Laravel\Prompts\info;

abstract class Server implements IsConsumer
{
    private PublisherContract $publisher;
    public $queue;
    public $exchange;

    public function __construct()
    {
        $this->queue = session('server_queue') ?? config('rabbitmq.rpc_queue', "");
        $this->exchange = config('rabbitmq.rpc_exchange');
        RabbitMQ::declareExchange($this->exchange, ExchangeType::TOPIC);
        if (! session('server_queue')) {
            list($this->queue,,) = RabbitMQ::declareQueue($this->queue);
        }
        session(['server_queue' => session('server_queue') ?? $this->queue]);
        $this->publisher = PublisherFactory::make('queue');

        $this->closeQueues();
    }

    private function closeQueues()
    {
        Helpers::queueDeleteOnExit($this->queue);
    }

    public function handler(AMQPMessage $req): void
    {
        if (! config('rabbitmq.router')) {
            $request = $req->getBody();
            info("Request received from client: " . $request);
            $this->handle($request);

            $this->respond($req);
            $req->ack();
        } else {
            $this->handle($req);
        }
        $this->closeQueues();
    }

    protected function respond(AMQPMessage $req)
    {
        $message = new Message($this->reply(), corr_id: $req->get('correlation_id'));

        $this->publisher->basic_publish($message, '', false, $req->get('reply_to'));
    }

    abstract public function handle($request): void;
    abstract protected function reply(): string;

    public function __destruct()
    {
        $this->closeQueues();
    }
}
