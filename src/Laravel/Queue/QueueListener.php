<?php namespace EventSourcing\Laravel\Queue;

use EventSourcing\EventDispatcher\EventDispatcher;
use EventSourcing\Serialization\Deserializer;
use Illuminate\Contracts\Queue\ShouldQueue;

class QueueListener implements ShouldQueue
{
    use Deserializer;

    protected  $dispatcher;

    public function __construct(EventDispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    public function handle($data)
    {
        $data = $this->deserialize(json_decode($data, true));

        $this->dispatcher->dispatch($data->event, $data->metadata);
    }


}
