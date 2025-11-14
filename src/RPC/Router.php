<?php

namespace KodePvt\RabbitmqLaravel\RPC;

use Exception;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use KodePvt\RabbitmqLaravel\Exceptions\RouteNotFoundException;
use KodePvt\RabbitmqLaravel\Facades\RabbitMQ;

use function Laravel\Prompts\info;
use function Laravel\Prompts\table;

class Router extends Server
{
    protected array $routes = [];
    protected string $reply = '';
    protected bool $routesLoaded = false;

    public function __construct()
    {
        parent::__construct();
        // dump('constructor', spl_object_id($this));
        // dump('--- Router constructed ---');
        // foreach (debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS) as $trace) {
        //     dump(($trace['file'] ?? '??') . ':' . ($trace['line'] ?? '??') . ' ' . ($trace['class'] ?? '') . '::' . ($trace['function'] ?? ''));
        // }
        // dump('----------------------------');
    }

    public function register(string $message, callable|array $callback): void
    {
        // dump('register', spl_object_id($this));
        // dump('--- Router register called ---');
        // foreach (debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS) as $trace) {
        //     dump(($trace['file'] ?? '??') . ':' . ($trace['line'] ?? '??') . ' ' . ($trace['class'] ?? '') . '::' . ($trace['function'] ?? ''));
        // }
        // dump('----------------------------');
        $regex = preg_replace('/\{(\w+)\}/', '(?P<$1>[^.]+)', $message);

        $regex = str_replace('*', '.*', $regex);

        $regex = '#^' . $regex . '$#';

        $this->routes[] = [
            'pattern' => $regex,
            'callback' => $callback,
            'original' => $message
        ];
    }


    protected function ensureRoutesLoaded(): void
    {
        if (! $this->routesLoaded && File::exists(base_path('routes/rabbit-rpc.php'))) {
            app('rabbit-router')->loadRoutesFileOnce();
            $this->routesLoaded = true;
        }
    }

    protected function loadRoutesFileOnce(): void
    {
        if (File::exists(base_path('routes/rabbit-rpc.php'))) {
            require_once base_path('routes/rabbit-rpc.php');
        }
    }

    public function handle($request): void
    {
        $router = app('rabbit-router');
        $router->ensureRoutesLoaded();

        $this->routes = $router->getRoutes();

        try {
            if ($body = json_decode($request->getBody(), true)) {
                $funcReturnValue = $this->callHandler(json_decode($request->getBody(), true));
                $this->reply = $funcReturnValue ?? 'null';
            } else throw new RouteNotFoundException("route not found.");
        } catch (RouteNotFoundException $e) {
            $this->reply = $e->getMessage();
        }

        $this->respond($request);
        $request->ack();
    }

    private function callHandler($message): mixed
    {
        if ($message && isset($message['route']) && isset($message['payload'])) {
            foreach ($this->routes as $route) {
                if (preg_match($route['pattern'], $message['route'], $matches)) {
                    $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                    $callback = $route['callback'];
                    if (is_array($callback) && is_string($callback[0]) && method_exists($callback[0], '__callStatic')) {
                        $callback = function (...$args) use ($callback, $message) {
                            return $callback[0]::__callStatic($callback[1], [json_decode($message['payload'], true), ...$args]);
                        };
                    }
                    $funcReturnValue = call_user_func_array($callback, $params);
                    info(now()->toDateTimeString() . ": {$message['route']}");
                    return $funcReturnValue;
                }
            }
        }
        $res = "route not found.";
        info(now()->toDateTimeString() . ": {$message['route']}, $res");
        throw new RouteNotFoundException("{$message['route']} $res");
    }

    public function reply(): string
    {
        return $this->reply;
    }

    public function getRoutes(): array
    {
        return $this->routes;
    }

    public function printRegisteredRoutes()
    {
        dd($this->routes);
    }
}
