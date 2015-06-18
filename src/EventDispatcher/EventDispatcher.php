<?php namespace EventSourcing\EventDispatcher;

interface EventDispatcher
{
    public function dispatch($event);

    public function project($event);

    public function addListener($eventName, $listener);

    public function addListeners($eventName, $listeners);
}
