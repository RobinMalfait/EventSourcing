<?php namespace EventSourcing\EventSourcing;

use EventSourcing\Domain\DomainEvent;
use ReflectionClass;

trait Replayer
{
    private $version = -1;

    public static function replayEvents($events)
    {
        return array_reduce($events, function ($me, $event) {
            return $me->applyAnEvent($event);
        }, new static);
    }

    public function applyAnEvent(DomainEvent $event)
    {
        $reflection = new ReflectionClass($event);
        $method = "apply" . $reflection->getShortName();

        if (method_exists($this, $method)) {
            call_user_func([$this, $method], $event);
        }

        $this->version++;

        return $this;
    }

    public function getVersion()
    {
        return $this->version;
    }
}
