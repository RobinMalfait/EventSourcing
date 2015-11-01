<?php namespace EventSourcing\Serialization;

interface Serializable
{
    /**
     * @return array
     */
    public function serialize();

    /**
     * @param array $data
     * @return mixed
     */
    public static function deserialize(array $data);
}
