<?php namespace EventSourcing\Domain;

use EventSourcing\Serialization\Serializable;

/**
 * Interface DomainEvent
 * @package EventSourcing\Domain
 */
interface DomainEvent extends Serializable
{
    /**
     * @return mixed
     */
    public function getAggregateId();

    /**
     * @return array
     */
    public function getMetaData();
}
