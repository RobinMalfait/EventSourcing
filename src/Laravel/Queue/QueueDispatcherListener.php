<?php namespace EventSourcing\Laravel\Queue;

use EventSourcing\EventDispatcher\EventDispatcher;
use EventSourcing\Serialization\Deserializer;
use Illuminate\Contracts\Queue\ShouldQueue;

class QueueDispatcherListener implements ShouldQueue
{
    use Deserializer;

    /**
     * @var EventDispatcher
     */
    protected $dispatcher;

    /**
     * @param EventDispatcher $dispatcher
     */
    public function __construct(EventDispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param $job
     * @param $transferObject
     */
    public function fire($job, $transferObject)
    {
        $transferObject = $this->deserialize(json_decode($transferObject, true));

        $this->dispatcher->dispatch($transferObject->getEvent(), $transferObject->getMetadata());

        $job->delete();
    }
}
