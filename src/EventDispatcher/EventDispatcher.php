<?php namespace EventSourcing\EventDispatcher;

interface EventDispatcher
{
    public function dispatch($event, $metadata = []);

    public function project($event, $metadata);

    public function addListener($eventName, $listener);

    public function addListeners($eventName, $listeners);
}
