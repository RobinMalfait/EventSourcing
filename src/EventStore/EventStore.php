<?php namespace EventSourcing\EventStore;

use Carbon\Carbon;
use EventSourcing\EventDispatcher\EventDispatcher;
use EventSourcing\Domain\MetaData;
use EventSourcing\EventDispatcher\TransferObject;
use EventSourcing\Laravel\Queue\QueueDispatcherListener;
use EventSourcing\Serialization\Serializer;
use Exception;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Logging\Log;
use Illuminate\Support\Facades\Queue;

abstract class EventStore
{
    /**
     * @var Log
     */
    protected $log;

    /**
     * @var EventDispatcher
     */
    protected $dispatcher;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->log = $app->make(Log::class);
        $this->config = $app->make(Config::class);
        $this->dispatcher = $app->make(EventDispatcher::class);
        $this->serializer = $app->make(Serializer::class);
    }

    /**
     * @param $aggregate
     * @return void
     */
    public function save($aggregate)
    {
        foreach ($aggregate->releaseEvents() as $event) {
            $aggregate->applyAnEvent($event);

            try {
                $metadata = new MetaData($event->getMetaData());

                $transferObject = new TransferObject(
                    $event,
                    $metadata->merge(
                        new MetaData([
                            'uuid' => $event->getAggregateId(),
                            'version' => $aggregate->getVersion(),
                            'type' => strtolower(str_replace("\\", ".", get_class($event))),
                            'recorded_on' => (string)(Carbon::now())
                        ])
                    )
                );

                $this->storeEvent($transferObject);

                if ($this->config->get('event_sourcing.autoqueue', false)) {
                    Queue::push(QueueDispatcherListener::class, json_encode($this->serializer->serialize($transferObject)));
                } else {
                    $this->dispatcher->dispatch(
                        $transferObject->getEvent(),
                        $transferObject->getMetadata()->serialize()
                    );
                }
            } catch (Exception $ex) {
                $this->log->error("An error has occurred while storing an event [" . $ex->getMessage() . "]", $ex->getTrace());
            }
        }
    }

    /**
     * @param $id
     * @return array
     */
    abstract public function getEventsFor($id);

    /**
     * @return array
     */
    abstract public function getAllEvents();

    /**
     * @param TransferObject $transferObject
     * @return mixed
     */
    abstract protected function storeEvent(TransferObject $transferObject);
}
