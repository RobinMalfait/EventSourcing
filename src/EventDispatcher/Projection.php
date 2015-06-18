<?php namespace EventSourcing\EventDispatcher;

use EventSourcing\Domain\DomainEvent;

interface Projection
{
    public function handle(DomainEvent $event);
}
