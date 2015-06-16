<?php namespace EventSourcing\EventStore;

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
     * @param $aggregateRoot
     * @return mixed
     */
    public function save($aggregateRoot);
}
