<?php namespace EventSourcing\EventDispatcher;

class EventSourcingEventDispatcher implements EventDispatcher
{
    private $listeners = [];

    public function dispatch($event)
    {
        if (is_array($event)) {
            foreach ($event as $e) {
                $this->dispatch($e);
            }

            return;
        }

        foreach ($this->getListeners(get_class($event)) as $listener)
        {
            $listener->handle($event);
        }
    }

    public function addListener($name, $listener)
    {
        if ($listener instanceof Listener) {
            $this->listeners[$name][] = $listener;
        } else if (is_string($listener)) {
            $this->addListener($name, app($listener));
        }
    }

    public function addListeners($name, $listeners)
    {
        foreach ($listeners as $listener) {
            $this->addListener($name, $listener);
        }
    }

    private function getListeners($name)
    {
        if (! $this->hasListeners($name)) {
            return [];
        }

        return $this->listeners[$name];
    }

    private function hasListeners($name)
    {
        return isset($this->listeners[$name]);
    }
}
