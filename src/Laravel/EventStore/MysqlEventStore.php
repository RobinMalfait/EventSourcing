<?php namespace EventSourcing\Laravel\EventStore;

use EventSourcing\EventDispatcher\TransferObject;
use EventSourcing\EventStore\EventStore;
use EventSourcing\Exceptions\NoEventsFoundException;
use EventSourcing\Serialization\Deserializer;
use EventSourcing\Serialization\Serializer;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Database\QueryException;

final class MysqlEventStore extends EventStore
{
    use Serializer, Deserializer;

    /**
     * @var
     */
    protected $db;

    /**
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        parent::__construct($app);

        $this->db = $app->make('db')->connection($this->getConnectionName());
    }

    /**
     * @param $id
     * @return array
     * @throws NoEventsFoundException
     */
    public function getEventsFor($id)
    {
        $rows = $this->db->table($this->getTableName())
            ->select(['uuid', 'version', 'payload', 'type', 'recorded_on'])
            ->where('uuid', $id)
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

    /**
     * @return array
     */
    public function getAllEvents()
    {
        return $this->db->table($this->getTableName())
            ->select(['uuid', 'version', 'payload', 'type', 'recorded_on'])
            ->get();
    }

    /**
     * @param TransferObject $transferObject
     * @return mixed
     */
    protected function storeEvent(TransferObject $transferObject)
    {
        $metadata = $transferObject->getMetadata();

        $this->db->beginTransaction();

        try {
            $this->db->table($this->getTableName())->insert([
                'uuid' => $metadata['uuid'],
                'version' => $metadata['version'],
                'payload' => json_encode($this->serialize($metadata['event'])),
                'recorded_on' => $metadata['recorded_on'],
                'type' => $metadata['type']
            ]);

            $this->db->commit();
        } catch (QueryException $ex) {
            $this->db->rollBack();
            throw $ex;
        }
    }

    /**
     * @return mixed
     */
    private function getTableName()
    {
        return $this->config->get('event_sourcing.table_name', 'eventstore');
    }

    /**
     * @return mixed
     */
    private function getConnectionName()
    {
        return $this->config->get('event_sourcing.connection_name', 'eventstore');
    }
}
