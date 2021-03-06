<?php namespace EventSourcing\EventDispatcher;

use EventSourcing\Domain\DomainEvent;
use EventSourcing\Domain\MetaData;
use EventSourcing\Laravel\Queue\QueueListenerExecuter;
use EventSourcing\Serialization\Serializer;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Queue;

/**
 * Class EventSourcingEventDispatcher
 * @package EventSourcing\EventDispatcher
 */
class EventSourcingEventDispatcher implements EventDispatcher
{
    /**
     * @var array
     */
    private $listeners = [];

    /**
     * @var array
     */
    private $projectors = [];

    /**
     * @var bool
     */
    private $status = false;

    /**
     * @param DomainEvent $event
     * @param MetaData $metadata
     */
    public function dispatch(DomainEvent $event, MetaData $metadata)
    {
        $this->project($event, $metadata);

        foreach ($this->getListeners(get_class($event)) as $listener) {
            $this->handle($listener, $event, $metadata->serialize());
        }
    }

    /**
     * @param DomainEvent $event
     * @param MetaData $metadata
     */
    public function project(DomainEvent $event, MetaData $metadata)
    {
        foreach ($this->getProjectors(get_class($event)) as $projector) {
            $this->handle($projector, $event, $metadata->serialize());
        }
    }

    /**
     * @param $name
     * @param $listener
     */
    public function addListener($name, $listener)
    {
        if ($listener instanceof Projection) {
            $this->projectors[$name][] = $listener;
        } elseif ($listener instanceof Listener) {
            $this->listeners[$name][] = $listener;
        } elseif (is_string($listener)) {
            $this->addListener($name, app()->make($listener));
        }
    }

    /**
     * @param $name
     * @param $listeners
     */
    public function addListeners($name, $listeners)
    {
        foreach ($listeners as $listener) {
            $this->addListener($name, $listener);
        }
    }

    /**
     * @param $status
     */
    public function rebuildMode($status)
    {
        $this->status = $status;
    }


    /**
     * @param $name
     * @return array
     */
    private function getListeners($name)
    {
        if (! $this->hasListeners($name)) {
            return [];
        }

        return $this->listeners[$name];
    }

    /**
     * @param $name
     * @return array
     */
    private function getProjectors($name)
    {
        if (! $this->hasProjectors($name)) {
            return [];
        }

        return $this->projectors[$name];
    }

    /**
     * @param $name
     * @return bool
     */
    private function hasListeners($name)
    {
        return isset($this->listeners[$name]);
    }

    /**
     * @param $name
     * @return bool
     */
    private function hasProjectors($name)
    {
        return isset($this->projectors[$name]);
    }

    /**
     * @param $listener
     * @param $event
     * @param $metadata
     */
    private function handle($listener, $event, $metadata)
    {
        if (! $this->status && $listener instanceof ShouldQueue) {
            $serializedTransferObject = Serializer::serialize(new TransferObject($event, MetaData::deserialize($metadata)));

            Queue::push(QueueListenerExecuter::class, [
                'listener' => get_class($listener),
                'transferObject' => json_encode($serializedTransferObject)
            ]);
        } else {
            $listener->handle($event, $metadata);
        }
    }
}
