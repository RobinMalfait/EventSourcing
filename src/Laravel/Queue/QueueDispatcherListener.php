<?php namespace EventSourcing\Laravel\Queue;

use EventSourcing\Domain\EventDispatcher;
use EventSourcing\Serialization\Serializer;
use Illuminate\Contracts\Queue\ShouldQueue;

class QueueDispatcherListener implements ShouldQueue
{
    /**
     * @var EventDispatcher
     */
    protected $dispatcher;

    /**
     * @var Serializer
     */
    protected $serializer;

    /**
     * @param EventDispatcher $dispatcher
     * @param Serializer $serializer
     */
    public function __construct(EventDispatcher $dispatcher, Serializer $serializer)
    {
        $this->dispatcher = $dispatcher;
        $this->serializer = $serializer;
    }

    /**
     * @param $job
     * @param $transferObject
     */
    public function fire($job, $transferObject)
    {
        $transferObject = $this->serializer->deserialize(json_decode($transferObject, true));

        $this->dispatcher->dispatch(
            $transferObject->getEvent(),
            $transferObject->getMetadata()->serialize()
        );

        $job->delete();
    }
}
