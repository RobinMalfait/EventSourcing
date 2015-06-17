<?php namespace EventSourcing\Laravel\EventStore;

use Carbon\Carbon;
use EventSourcing\EventStore\EventStore;
use EventSourcing\Serialization\Deserializer;
use EventSourcing\Serialization\Serializer;
use Illuminate\Database\DatabaseManager;

final class MysqlEventStore implements EventStore
{
    use Serializer, Deserializer;

    public function __construct(DatabaseManager $databaseManager)
    {
        $this->db = $databaseManager;
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
                $aggregate->getPlayhead(),
                $this->serialize($event),
                strtolower(str_replace("\\", ".", get_class($event)))
            );
        }

        $this->dispatcher->dispatch((new \ReflectionClass($aggregate))->getName(), $aggregate->releaseEvents());
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
     * @param $playhead
     * @param $payload
     * @param $type
     */
    private function storeEvent($uuid, $playhead, $payload, $type)
    {
        $this->db->beginTransaction();

        try {
            $this->db->connection('eventstore')->table('eventstore')->insert([
                'uuid' => $uuid,
                'playhead' => $playhead,
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
        $rows = $this->db->connection('eventstore')->table('eventstore')
                ->select(['uuid', 'playhead', 'payload', 'recorded_on', 'type'])
                ->where('uuid', $uuid)
                ->orderBy('playhead', 'asc')
                ->get();

        $events = [];

        foreach ($rows as $row) {
            $events[] = $this->deserialize($row->payload);
        }

        return $events;
    }
}
