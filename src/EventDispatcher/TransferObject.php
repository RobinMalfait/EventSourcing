<?php namespace EventSourcing\EventDispatcher;

use EventSourcing\Domain\DomainEvent;
use Illuminate\Contracts\Support\Arrayable;

class TransferObject implements Arrayable
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
    public function __construct(DomainEvent $event, MetaData $metadata)
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
     * @return array
     */
    public function getMetadata()
    {
        return $this->metadata->getData();
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->metadata->getData();
    }
}
