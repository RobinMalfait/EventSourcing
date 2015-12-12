<?php namespace EventSourcing\Laravel\EventStore;

use EventSourcing\Domain\MetaData;
use EventSourcing\EventDispatcher\TransferObject;
use EventSourcing\EventStore\EventStore;
use EventSourcing\Exceptions\NoEventsFoundException;
use EventSourcing\Serialization\Serializer;
use Illuminate\Database\Connection;
use Illuminate\Database\QueryException;

final class MysqlEventStore extends EventStore
{
    /**
     * @var Connection
     */
    protected $db;

    /**
     * @var string
     */
    private $table;

    /**
     *
     */
    public function __construct()
    {
        parent::__construct();

        $this->db = app()->make('db');
        $this->table = $this->getTableName();
    }

    private function setDB()
    {
        if (! $this->db instanceof Connection) {
            $this->db = $this->db->connection($this->getConnectionName());
        }
    }


    /**
     * @param $id
     * @return array
     * @throws NoEventsFoundException
     */
    public function getEventsFor($id)
    {
        $this->setDB();

        $rows = $this->db->table($this->table)
            ->select(['uuid', 'version', 'payload', 'metadata', 'type', 'recorded_on'])
            ->where('uuid', $id)
            ->orderBy('version', 'asc')
            ->get();

        if (! $rows) {
            throw new NoEventsFoundException();
        }

        $events = [];

        foreach ($rows as $row) {
            $events[] = Serializer::deserialize(json_decode($row->payload, true));
        }

        return $events;
    }

    /**
     * @return array
     */
    public function getAllEvents()
    {
        $this->setDB();

        return $this->db->table($this->getTableName())
            ->select(['uuid', 'version', 'payload', 'metadata', 'type', 'recorded_on'])
            ->get();
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

    /**
     * @param TransferObject $transferObject
     * @param MetaData $metaData
     * @return mixed
     */
    protected function storeEvent(TransferObject $transferObject, MetaData $metaData)
    {
        $this->setDB();

        $this->db->beginTransaction();

        try {
            $this->db->table($this->table)->insert([
                'uuid' => $metaData->get('uuid'),
                'version' => $metaData->get('version'),
                'payload' => json_encode(Serializer::serialize($transferObject->getEvent())),
                'metadata' => json_encode(Serializer::serialize($transferObject->getMetadata())),
                'recorded_on' => $metaData->get('recorded_on'),
                'type' => $metaData->get('type')
            ]);

            $this->db->commit();
        } catch (QueryException $ex) {
            $this->db->rollBack();
            throw $ex;
        }
    }
}
