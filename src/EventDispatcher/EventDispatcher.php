<?php namespace EventSourcing\EventDispatcher;

interface EventDispatcher
{
    public function dispatch($event);

    public function addListener($eventName, Listener $listener);

    public function addListeners($eventName, $listeners);
}
