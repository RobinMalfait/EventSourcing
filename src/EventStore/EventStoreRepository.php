<?php namespace EventSourcing\EventStore;

use EventSourcing\Domain\AggregateRoot;

interface EventStoreRepository
{
    /**
     * @param $class
     * @return mixed
     */
    public function setAggregateClass($class);

    /**
     * @param $id
     * @return array
     */
    public function load($id);

    /**
     * @param AggregateRoot $aggregateRoot
     * @return mixed
     */
    public function save(AggregateRoot $aggregateRoot);
}
