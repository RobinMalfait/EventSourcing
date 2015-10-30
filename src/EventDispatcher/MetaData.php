<?php namespace EventSourcing\EventDispatcher;

use Carbon\Carbon;

class MetaData
{
    private $uuid;

    private $version;

    private $type;

    private $recordedOn;

    public function __construct($uuid, $version, $type, $recordedOn)
    {
        $this->uuid = $uuid;
        $this->version = $version;
        $this->type = $type;
        $this->recordedOn = $recordedOn;
    }

    public static function fromArray(array $data)
    {
        return new static(
            $data['uuid'],
            $data['version'],
            $data['type'],
            (string) $data['recorded_on']
        );
    }

    /**
     * @return array
     */
    public function getData()
    {
        return [
            'uuid' => $this->uuid,
            'version' => $this->version,
            'type' => $this->type,
            'recorded_on' => new Carbon($this->recordedOn)
        ];
    }
}
