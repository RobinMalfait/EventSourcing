<?php namespace EventSourcing\EventDispatcher;

use EventSourcing\Domain\DomainEvent;

trait EventGenerator
{
    private $recordedEvents = [];

    public function releaseEvents()
    {
        $events = $this->recordedEvents;

        $this->recordedEvents = [];

        return $events;
    }

    public function apply(DomainEvent $event)
    {
        $this->recordedEvents[] = $event;
    }
}
