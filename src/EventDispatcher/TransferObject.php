<?php namespace EventSourcing\EventDispatcher;

use EventSourcing\Domain\DomainEvent;
use EventSourcing\Domain\MetaData;
use EventSourcing\Serialization\Serializable;
use EventSourcing\Serialization\Serializer;

/**
 * Class TransferObject
 * @package EventSourcing\EventDispatcher
 */
class TransferObject implements Serializable
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
     * @return array
     */
    public function serialize()
    {
        return [
            'event' => Serializer::serialize($this->event),
            'metadata' => Serializer::serialize($this->metadata)
        ];
    }

    /**
     * @param array $data
     * @return mixed
     */
    public static function deserialize(array $data)
    {
        return new static(
            Serializer::deserialize($data['event']),
            Serializer::deserialize($data['metadata'])
        );
    }

    /**
     * @return MetaData
     */
    public function getMetadata()
    {
        return $this->metadata;
    }

    /**
     * @return DomainEvent
     */
    public function getEvent()
    {
        return $this->event;
    }
}
