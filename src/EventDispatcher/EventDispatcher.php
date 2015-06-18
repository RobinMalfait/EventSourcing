<?php namespace EventSourcing\EventDispatcher;

interface EventDispatcher
{
    public function dispatch($event);

    public function addProjector($projector);

    public function addProjectors($projectors);

    public function project($event);

    public function addListener($eventName, $listener);

    public function addListeners($eventName, $listeners);
}
