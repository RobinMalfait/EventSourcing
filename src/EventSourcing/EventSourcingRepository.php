<?php namespace EventSourcing\EventSourcing;

use EventSourcing\Domain\AggregateRoot;
use EventSourcing\EventStore\EventStore;
use EventSourcing\EventStore\EventStoreRepository;
use EventSourcing\Exceptions\AggregateClassNotFoundException;

class EventSourcingRepository implements EventStoreRepository
{
    /**
     * @var EventStore
     */
    protected $eventStore;

    /**
     * @var AggregateRoot
     */
    protected $aggregateClass;

    /**
     * @param EventStore $eventStore
     */
    public function __construct(EventStore $eventStore)
    {
        $this->eventStore = $eventStore;
    }

    /**
     * @param $class
     * @return mixed
     */
    public function setAggregateClass($class)
    {
        $this->aggregateClass = $class;
    }

    /**
     * @param $id
     * @return array
     * @throws AggregateClassNotFoundException
     */
    public function load($id)
    {
        $subject = $this->aggregateClass;

        if (! $subject) {
            throw new AggregateClassNotFoundException();
        }

        return $subject::replayEvents($this->eventStore->getEventsFor($id));
    }

    /**
     * @param AggregateRoot $aggregateRoot
     * @return mixed
     */
    public function save(AggregateRoot $aggregateRoot)
    {
        return $this->eventStore->save($aggregateRoot);
    }
}
