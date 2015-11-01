<?php namespace EventSourcing\Domain;

use EventSourcing\Serialization\Serializable;

class MetaData implements Serializable
{
    /**
     * @var array
     */
    private $data;

    /**
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * @param MetaData $metaData
     * @return MetaData
     */
    public function merge(MetaData $metaData)
    {
        return new Metadata(
            array_merge($this->data, $metaData->data)
        );
    }


    /**
     * @return array
     */
    public function serialize()
    {
        return $this->data;
    }

    /**
     * @param array $data
     * @return mixed
     */
    public static function deserialize(array $data)
    {
        return new static($data);
    }
}
