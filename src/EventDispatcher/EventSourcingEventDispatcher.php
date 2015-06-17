<?php namespace EventSourcing\EventDispatcher;

class EventSourcingEventDispatcher implements EventDispatcher
{
    private $listeners = [];

    public function dispatch($eventName, $event)
    {
        if (is_array($event)) {
            foreach ($event as $e) {
                $this->dispatch($eventName, $e);
            }

            return;
        }

        foreach ($this->getListeners($eventName) as $listener)
        {
            $listener->handle($event);
        }
    }

    public function addListener($name, Listener $listener)
    {
        $this->listeners[$name][] = $listener;
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
