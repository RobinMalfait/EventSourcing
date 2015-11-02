<?php namespace EventSourcing\EventDispatcher;

use EventSourcing\Domain\DomainEvent;
use EventSourcing\Domain\MetaData;

/**
 * Interface EventDispatcher
 * @package EventSourcing\EventDispatcher
 */
interface EventDispatcher
{
    /**
     * @param DomainEvent $event
     * @param MetaData $metadata
     */
    public function dispatch(DomainEvent $event, MetaData $metadata);

    /**
     * @param DomainEvent $event
     * @param MetaData $metadata
     */
    public function project(DomainEvent $event, MetaData $metadata);

    /**
     * @param $eventName
     * @param $listener
     */
    public function addListener($eventName, $listener);

    /**
     * @param $eventName
     * @param $listeners
     */
    public function addListeners($eventName, $listeners);
}
