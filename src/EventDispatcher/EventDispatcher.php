<?php namespace EventSourcing\EventDispatcher;

use EventSourcing\Domain\DomainEvent;
use EventSourcing\Domain\MetaData;

interface EventDispatcher
{
    public function dispatch(DomainEvent $event, MetaData $metadata);

    public function project(DomainEvent $event, MetaData $metadata);

    public function addListener($eventName, $listener);

    public function addListeners($eventName, $listeners);
}
