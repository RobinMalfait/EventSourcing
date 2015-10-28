<?php namespace EventSourcing\EventStore;

use Carbon\Carbon;
use EventSourcing\EventDispatcher\EventDispatcher;
use EventSourcing\EventDispatcher\MetaData;
use EventSourcing\EventDispatcher\TransferObject;
use EventSourcing\Laravel\Queue\QueueDispatcherListener;
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

    public function __construct(Application $app)
    {
        $this->log = $app->make(Log::class);
        $this->config = $app->make(Config::class);
        $this->dispatcher = $app->make(EventDispatcher::class);
    }

    /**
     * @param $aggregate
     * @return void
     */
    public function save($aggregate)
    {
        foreach ($aggregate->releaseEvents() as $event) {
            $aggregate->applyAnEvent($event);

            $uuid = $event->getAggregateId();
            $version = $aggregate->getVersion();
            $type = strtolower(str_replace("\\", ".", get_class($event)));
            $recordedOn = Carbon::now();

            try {
                $transferObject = new TransferObject(
                    $event,
                    new MetaData(
                        $uuid,
                        $version,
                        $type,
                        (string) $recordedOn
                    )
                );

                $this->storeEvent($transferObject);

                if ($this->config->get('event_sourcing.autoqueue', false)) {
                    Queue::push(QueueDispatcherListener::class, json_encode($this->serialize($transferObject)));
                } else {
                    $this->dispatcher->dispatch($transferObject->getEvent(), $transferObject->getMetadata());
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
