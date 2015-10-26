<?php namespace EventSourcing\Laravel\EventStore;

use Carbon\Carbon;
use EventSourcing\EventDispatcher\MetaData;
use EventSourcing\EventDispatcher\TransferObject;
use EventSourcing\EventStore\EventStore;
use EventSourcing\Exceptions\NoEventsFoundException;
use EventSourcing\Laravel\Queue\QueueListener;
use EventSourcing\Serialization\Deserializer;
use EventSourcing\Serialization\Serializer;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Database\QueryException;
use Illuminate\Contracts\Logging\Log;
use Illuminate\Support\Facades\Queue;

final class MysqlEventStore implements EventStore
{
    use Serializer, Deserializer;

    protected $db;

    private $table = "eventstore";

    /**
     * @var Log
     */
    protected $log;

    public function __construct(Application $app)
    {
        $this->log = $app->make(Log::class);
        $this->db = $app->make('db')->connection($this->table);
    }

    /**
     * @param $aggregate
     * @return mixed
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
                $this->storeEvent($uuid, $version, json_encode($this->serialize($event)), $recordedOn, $type);

                $metadata = [
                    'uuid' => $uuid,
                    'version' => $version,
                    'type' => $type,
                    'recorded_on' => $recordedOn
                ];

                $obj = new TransferObject(
                    $event,
                    new MetaData($metadata)
                );

                Queue::push(QueueListener::class, json_encode($this->serialize($obj)));
            } catch (QueryException $ex) {
                $this->log->error("An error has occurred while storing an event [" . $ex->getMessage() . "]", $ex->getTrace());
            }
        }
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getEventsFor($id)
    {
        return $this->searchEventsFor($id);
    }

    /**
     * @param $uuid
     * @param $version
     * @param $payload
     * @param $recordedOn
     * @param $type
     */
    private function storeEvent($uuid, $version, $payload, $recordedOn, $type)
    {
        $this->db->beginTransaction();

        try {
            $this->db->table($this->table)->insert([
                'uuid' => $uuid,
                'version' => $version,
                'payload' => $payload,
                'recorded_on' => $recordedOn,
                'type' => $type
            ]);

            $this->db->commit();
        } catch (QueryException $ex) {
            $this->db->rollBack();
            throw $ex;
        }
    }

    /**
     * @param $uuid
     * @return array
     * @throws NoEventsFoundException
     */
    private function searchEventsFor($uuid)
    {
        $rows = $this->db->table($this->table)
                ->select(['uuid', 'version', 'payload', 'type', 'recorded_on'])
                ->where('uuid', $uuid)
                ->orderBy('version', 'asc')
                ->get();

        if (! $rows) {
            throw new NoEventsFoundException();
        }

        $events = [];

        foreach ($rows as $row) {
            $events[] = $this->deserialize(json_decode($row->payload, true));
        }

        return $events;
    }
}
