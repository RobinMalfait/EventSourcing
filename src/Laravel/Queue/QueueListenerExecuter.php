<?php namespace EventSourcing\Laravel\Queue;

use EventSourcing\EventDispatcher\TransferObject;
use EventSourcing\Serialization\Deserializer;
use EventSourcing\Serialization\Serializer;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Queue\ShouldQueue;

class QueueListenerExecuter implements ShouldQueue
{
    use Deserializer;

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
     * @var Serializer
     */
    private $serializer;

    /**
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->serializer = $app->make(Serializer::class);
    }

    /**
     * @param $job
     * @param $data
     */
    public function fire($job, $data)
    {
        $this->listener = app()->make($data['listener']);
        $this->transferObject = $this->serializer->deserialize(json_decode($data['transferObject'], true));

        $this->listener->handle(
            $this->transferObject->getEvent(),
            $this->transferObject->getMetadata()->serialize()
        );

        $job->delete();
    }
}
