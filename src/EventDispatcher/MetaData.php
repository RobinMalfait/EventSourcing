<?php namespace EventSourcing\EventDispatcher;

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

    /**
     * @return array
     */
    public function getData()
    {
        return [
            'uuid' => $this->uuid,
            'version' => $this->version,
            'type' => $this->type,
            'recorded_on' => $this->recordedOn
        ];
    }
}
