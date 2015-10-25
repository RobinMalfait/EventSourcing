<?php namespace EventSourcing\EventSourcing;

trait Dispatcher
{
    private $app;

    public function __construct()
    {
        $this->app = app();
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
