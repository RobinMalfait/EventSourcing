<?php namespace EventSourcing\Laravel\Queue;

use EventSourcing\EventDispatcher\TransferObject;
use EventSourcing\Serialization\Serializer;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Queue\ShouldQueue;

class QueueListenerExecuter implements ShouldQueue
{
    /**
     * @var Application
     */
    private $app;

    /**
     * @var
     */
    private $listener;

    /**
     * @var TransferObject
     */
    private $transferObject;

    /**
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * @param $job
     * @param $data
     */
    public function fire($job, $data)
    {
        $this->listener = app()->make($data['listener']);
        $this->transferObject = Serializer::deserialize(json_decode($data['transferObject'], true));

        $this->listener->handle(
            $this->transferObject->getEvent(),
            $this->transferObject->getMetadata()->serialize()
        );

        $job->delete();
    }
}
