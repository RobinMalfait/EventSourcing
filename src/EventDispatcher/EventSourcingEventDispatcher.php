<?php namespace EventSourcing\EventDispatcher;

use Illuminate\Contracts\Queue\ShouldQueue;

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
     * @param $event
     * @param array $metadata
     */
    public function dispatch($event, $metadata = [])
    {
        $this->project($event, $metadata);

        foreach ($this->getListeners(get_class($event)) as $listener) {
            $this->handle($listener, $event, $metadata);
        }
    }

    /**
     * @param $event
     * @param array $metadata
     */
    public function project($event, $metadata = [])
    {
        foreach ($this->projectors as $projector) {
            $this->handle($projector, $event, $metadata);
        }

        foreach ($this->getListeners(get_class($event)) as $listener) {
            if ($listener instanceof Projection) {
                $this->handle($listener, $event, $metadata);
            }
        }
    }

    /**
     * @param $projector
     */
    public function addProjector($projector)
    {
        if (is_string($projector)) {
            $this->addProjector(app()->make($projector));
            return;
        }

        $this->projectors[] = $projector;
    }

    /**
     * @param $projectors
     */
    public function addProjectors($projectors)
    {
        foreach ($projectors as $projector) {
            $this->addProjector($projector);
        }
    }

    /**
     * @param $name
     * @param $listener
     */
    public function addListener($name, $listener)
    {
        if ($listener instanceof Listener) {
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
     * @return bool
     */
    private function hasListeners($name)
    {
        return isset($this->listeners[$name]);
    }

    /**
     * @param $listener
     * @param $event
     * @param $metadata
     */
    private function handle($listener, $event, $metadata)
    {
        if ($listener instanceof ShouldQueue) {
            Queue::push(function () use ($listener, $event, $metadata) {
                $listener->handle($event, $metadata);
            });
        } else {
            $listener->handle($event, $metadata);
        }
    }
}
