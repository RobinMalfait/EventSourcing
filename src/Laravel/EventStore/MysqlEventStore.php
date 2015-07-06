<?php namespace EventSourcing\Laravel\EventStore;

use Carbon\Carbon;
use EventSourcing\EventDispatcher\EventDispatcher;
use EventSourcing\EventStore\EventStore;
use EventSourcing\Exceptions\NoEventsFoundException;
use EventSourcing\Serialization\Deserializer;
use EventSourcing\Serialization\Serializer;
use Illuminate\Contracts\Foundation\Application;

final class MysqlEventStore implements EventStore
{
    use Serializer, Deserializer;

    protected $dispatcher;

    public function __construct(Application $app, EventDispatcher $dispatcher)
    {
        $this->db = $app['db']->connection('eventstore');
        $this->dispatcher = $dispatcher;
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

            $this->storeEvent($uuid, $version, json_encode($this->serialize($event)), $recordedOn, $type);

            $metadata = [
                'uuid' => $uuid,
                'version' => $version,
                'type' => $type,
                'recorded_on' => $recordedOn
            ];

            $this->dispatcher->dispatch($event, $metadata);
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
            $this->db->table('eventstore')->insert([
                'uuid' => $uuid,
                'version' => $version,
                'payload' => $payload,
                'recorded_on' => $recordedOn,
                'type' => $type
            ]);

            $this->db->commit();
        } catch (QueryException $ex) {
            $this->db->rollBack();
        }
    }

    /**
     * @param $uuid
     * @return array
     * @throws NoEventsFoundException
     */
    private function searchEventsFor($uuid)
    {
        $rows = $this->db->table('eventstore')
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
