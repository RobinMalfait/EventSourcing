<?php namespace EventSourcing\EventDispatcher;

interface EventDispatcher
{
    public function dispatch($eventName, $event);

    public function addListener($name, Listener $listener);

    public function addListeners($name, $listeners);
}
