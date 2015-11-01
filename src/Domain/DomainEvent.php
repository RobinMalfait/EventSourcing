<?php namespace EventSourcing\Domain;

use EventSourcing\Serialization\Serializable;

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
