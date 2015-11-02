<?php namespace EventSourcing\EventDispatcher;

use EventSourcing\Domain\DomainEvent;

/**
 * Class EventGenerator
 * @package EventSourcing\EventDispatcher
 */
trait EventGenerator
{
    /**
     * @var array
     */
    private $recordedEvents = [];

    /**
     * @return array
     */
    public function releaseEvents()
    {
        $events = $this->recordedEvents;

        $this->recordedEvents = [];

        return $events;
    }

    /**
     * @param DomainEvent $event
     */
    public function apply(DomainEvent $event)
    {
        $this->recordedEvents[] = $event;
    }
}
