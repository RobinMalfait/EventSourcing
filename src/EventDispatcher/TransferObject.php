<?php namespace EventSourcing\EventDispatcher;

use EventSourcing\Domain\DomainEvent;

final class TransferObject
{
    /**
     * @var DomainEvent
     */
    private $event;

    /**
     * @var MetaData
     */
    private $metadata;

    /**
     * @param DomainEvent $event
     * @param MetaData $metadata
     */
    public function __constructor(DomainEvent $event, MetaData $metadata)
    {
        $this->event = $event;
        $this->metadata = $metadata;
    }

    /**
     * @return DomainEvent
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * @return MetaData
     */
    public function getMetadata()
    {
        return $this->metadata->getData();
    }
}
