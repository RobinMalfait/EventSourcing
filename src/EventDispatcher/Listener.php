<?php namespace EventSourcing\EventDispatcher;

use EventSourcing\Domain\DomainEvent;

interface Listener
{
    public function handle(DomainEvent $event);
}
