<?php namespace EventSourcing\EventSourcing;

trait Dispatcher
{
    /**
     * @param $command
     * @return mixed
     */
    public function dispatch($command)
    {
        return $this->getHandlerFor($command)->handle($command);
    }

    /**
     * @param $command
     * @return mixed
     */
    private function getHandlerFor($command)
    {
        $handler = get_class($command) . "Handler";

        return app()->make($handler);
    }
}
