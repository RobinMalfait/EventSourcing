<?php namespace EventSourcing\Laravel\Queue;

use EventSourcing\EventDispatcher\EventDispatcher;
use Illuminate\Contracts\Queue\ShouldQueue;
use stdClass;

class QueueListener implements ShouldQueue
{

    protected  $dispatcher;

    public function __construct(EventDispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    public function handle(stdClass $data)
    {
        $this->dispatcher->dispatch($data->event, $data->metadata);
    }


}
