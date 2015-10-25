<?php namespace EventSourcing\Laravel\Queue;

use EventSourcing\EventDispatcher\EventDispatcher;

class QueueListener {

    protected  $dispatcher;

    public function __construct(EventDispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    public function handle($job, $data)
    {
        $this->dispatcher->dispatch($data['event'], $data['metadata']);

        $job->delete();
    }


}
