<?php namespace EventSourcing\Laravel\Queue;

use EventSourcing\EventDispatcher\EventDispatcher;
use EventSourcing\Serialization\Deserializer;
use Illuminate\Contracts\Queue\ShouldQueue;

class QueueListener implements ShouldQueue
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
     * @param $data
     */
    public function handle($data)
    {
        $data = $this->deserialize(json_decode($data, true));

        $this->dispatcher->dispatch($data->getEvent(), $data->getMetadata());
    }
}
