<?php namespace EventSourcing\Domain;

use EventSourcing\Serialization\Serializable;

/**
 * Class MetaData
 * @package EventSourcing\Domain
 */
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
     * @param $key
     * @param null $default
     * @return static
     */
    public function get($key, $default = null)
    {
        return collect($this->data)->get($key, $default);
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
