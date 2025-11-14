<?php

return [
    /*
    |-------------------------------------------------------------------
    | RabbitMQ Configuration
    |-------------------------------------------------------------------
    | Here you may define all of the connection settings for RabbitMQ.
    | These options control how your application connects
    | to and interacts with your RabbitMQ broker.
    |
    */

    'host' => env('RABBITMQ_HOST', 'localhost'),
    'port' => env('RABBITMQ_PORT', 5672),
    'user' => env('RABBITMQ_USER', 'guest'),
    'pass' => env('RABBITMQ_PASSWORD', 'guest'),
    'vhost' => env('RABBITMQ_VHOST', '/'),
    'queue' => env('RABBITMQ_QUEUE', 'default'),

    /*
    |-------------------------------------------------------------------
    | Consumers
    |-------------------------------------------------------------------
    | Consumers classes will be generated in this namespace when
    | rabbit:make-consumer is executed. Consumers subscribe
    | and handle the messages on the provided queue.
    |
    */

    'consumer_namespace' => 'App\RabbitMQ\Consumers',

    /*
    |-------------------------------------------------------------------
    | Handlers
    |-------------------------------------------------------------------
    | Handlers will be generated in this namespace when
    | rabbit:make-handler is executed. Handlers are
    | the controllers of your rpc requests.
    |
    */

    'handler_namespace' => 'App\RabbitMQ\RPC\Handlers',

    /*
    |-------------------------------------------------------------------
    | RPC Configuration
    |-------------------------------------------------------------------
    | These configuration are for your rpc server and rpc client
    | "rpc_queue" is the name of the queue which will be auto
    | declared and consumed by the servers and clients.
    |
    | rpc_queue: leave blank to declare a queue with random identifier
    |
    */

    'rpc_queue' => '',

    'rpc_exchange' => 'rpc_exchange', // topic exchange

    'rpc_topics' => [
        // "*.foo.#"
    ], // use --topic=x,y,z to override these topic on runtime

    'rpc_namespaces' => [
        'client' => 'App\RabbitMQ\RPC\Clients',
        'server' => 'App\RabbitMQ\RPC\Servers'
    ],

    'router' => true,
];
