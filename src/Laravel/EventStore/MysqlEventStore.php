<?php namespace EventSourcing\Laravel\EventStore;

use Carbon\Carbon;
use EventSourcing\EventDispatcher\EventDispatcher;
use EventSourcing\EventStore\EventStore;
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

            $this->storeEvent(
                $event->getAggregateId(),
                $aggregate->getVersion(),
                json_encode($this->serialize($event)),
                strtolower(str_replace("\\", ".", get_class($event)))
            );

            $this->dispatcher->dispatch($event);
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
     * @param $type
     */
    private function storeEvent($uuid, $version, $payload, $type)
    {
        $this->db->beginTransaction();

        try {
            $this->db->table('eventstore')->insert([
                'uuid' => $uuid,
                'version' => $version,
                'payload' => $payload,
                'recorded_on' => Carbon::now(),
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
     */
    private function searchEventsFor($uuid)
    {
        $rows = $this->db->table('eventstore')
                ->select(['uuid', 'version', 'payload', 'type', 'recorded_on'])
                ->where('uuid', $uuid)
                ->orderBy('version', 'asc')
                ->get();

        $events = [];

        foreach ($rows as $row) {
            $events[] = $this->deserialize(json_decode($row->payload, true));
        }

        return $events;
    }
}
