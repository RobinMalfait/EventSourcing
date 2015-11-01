<?php namespace EventSourcing\EventDispatcher;

interface Projection extends Listener
{
    public function handle(DomainEvent $event, $metadata = []);
}
