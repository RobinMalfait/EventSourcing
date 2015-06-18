<?php namespace EventSourcing\EventDispatcher;

use EventSourcing\Domain\DomainEvent;

interface Projection extends Listener
{
    public function handle(DomainEvent $event);
}
