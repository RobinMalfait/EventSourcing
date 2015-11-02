<?php namespace EventSourcing\EventDispatcher;

use EventSourcing\Domain\DomainEvent;

/**
 * Interface Listener
 * @package EventSourcing\EventDispatcher
 */
interface Listener
{
    /**
     * @param DomainEvent $event
     * @param array $metadata
     */
    public function handle(DomainEvent $event, $metadata = []);
}
