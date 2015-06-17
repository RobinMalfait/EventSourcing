<?php namespace EventSourcing\EventStore;

interface EventStore
{
    /**
     * @param $aggregate
     * @return mixed
     */
    public function save($aggregate);

    /**
     * @param $id
     * @return mixed
     */
    public function getEventsFor($id);
}
