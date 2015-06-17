<?php namespace EventSourcing\EventSourcing;

use Illuminate\Contracts\Foundation\Application;

final class Dispatcher
{
    private $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function dispatch($command)
    {
        return $this->getHandlerFor($command)->handle($command);
    }

    private function getHandlerFor($command)
    {
        $handler = get_class($command) . "Handler";

        return $this->app->make($handler);
    }
}
