<?php namespace EventSourcing\EventSourcing;

use EventSourcing\Domain\DomainEvent;

/**
 * Class Replayer
 * @package EventSourcing\EventSourcing
 */
trait Replayer
{
    /**
     * @var int
     */
    private $version = -1;

    /**
     * @param $events
     * @return mixed
     */
    public static function replayEvents($events)
    {
        return array_reduce($events, function ($me, $event) {
            return $me->applyAnEvent($event);
        }, new static);
    }

    /**
     * @param DomainEvent $event
     * @return $this
     */
    public function applyAnEvent(DomainEvent $event)
    {
        $classParts = explode('\\', get_class($event));
        $method = "apply" . end($classParts);

        if (method_exists($this, $method)) {
            call_user_func([$this, $method], $event);
        }

        $this->version++;

        return $this;
    }

    /**
     * @return int
     */
    public function getVersion()
    {
        return $this->version;
    }
}
