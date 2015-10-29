<?php namespace EventSourcing\Laravel\Queue;

use EventSourcing\EventDispatcher\TransferObject;
use EventSourcing\Serialization\Deserializer;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Queue\ShouldQueue;

class QueueListenerExecuter implements ShouldQueue
{
    use Deserializer;

    private $app;

    private $listener;

    /**
     * @var TransferObject
     */
    private $transferObject;

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
        $this->transferObject = $this->deserialize(json_decode($data['transferObject'], true));

        $this->listener->handle(
            $this->transferObject->getEvent(),
            $this->transferObject->getMetadata()
        );

        $job->delete();
    }
}
