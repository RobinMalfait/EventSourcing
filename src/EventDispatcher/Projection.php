<?php namespace EventSourcing\EventDispatcher;

use EventSourcing\Domain\DomainEvent;

/**
 * Interface Projection
 * @package EventSourcing\EventDispatcher
 */
interface Projection extends Listener
{
    /**
     * @param DomainEvent $event
     * @param array $metadata
     */
    public function handle(DomainEvent $event, $metadata = []);
}
