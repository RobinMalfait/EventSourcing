<?php namespace EventSourcing\EventDispatcher;

class EventSourcingEventDispatcher implements EventDispatcher
{
    private $listeners = [];

    private $projectors = [];

    public function dispatch($event, $metadata = [])
    {
        if (is_array($event)) {
            foreach ($event as $e) {
                $this->dispatch($e, $metadata);
            }

            return;
        }

        $this->project($event, $metadata);

        foreach ($this->getListeners(get_class($event)) as $listener) {
            $listener->handle($event, $metadata);
        }
    }

    public function project($event, $metadata = [])
    {
        if (is_array($event)) {
            foreach ($event as $e) {
                $this->dispatch($e);
            }

            return;
        }

        foreach ($this->projectors as $projector) {
            $projector->handle($event, $metadata);
        }

        foreach ($this->getListeners(get_class($event)) as $listener) {
            if ($listener instanceof Projection) {
                $listener->handle($event, $metadata);
            }
        }
    }

    public function addProjector($projector)
    {
        if (is_string($projector)) {
            $this->addProjector(app($projector));
            return;
        }

        $this->projectors[] = $projector;
    }

    public function addProjectors($projectors)
    {
        foreach ($projectors as $projector) {
            $this->addProjector($projector);
        }
    }

    public function addListener($name, $listener)
    {
        if ($listener instanceof Listener) {
            $this->listeners[$name][] = $listener;
        } elseif (is_string($listener)) {
            $this->addListener($name, app($listener));
        }
    }

    public function addListeners($name, $listeners)
    {
        foreach ($listeners as $listener) {
            $this->addListener($name, $listener);
        }
    }

    private function getListeners($name)
    {
        if (! $this->hasListeners($name)) {
            return [];
        }

        return $this->listeners[$name];
    }

    private function hasListeners($name)
    {
        return isset($this->listeners[$name]);
    }
}
