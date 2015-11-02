<?php namespace EventSourcing\Laravel\Queue;

use EventSourcing\EventDispatcher\EventDispatcher;
use EventSourcing\Serialization\Serializer;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * Class QueueDispatcherListener
 * @package EventSourcing\Laravel\Queue
 */
class QueueDispatcherListener implements ShouldQueue
{
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
        $transferObject = Serializer::deserialize(json_decode($transferObject, true));

        $this->dispatcher->dispatch(
            $transferObject->getEvent(),
            $transferObject->getMetadata()
        );

        $job->delete();
    }
}
