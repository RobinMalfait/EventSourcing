<?php namespace EventSourcing\Serialization;

interface Serializer
{
    /**
     * @param $object
     * @return array
     */
    public function serialize(Serializable $object);

    /**
     * @param array $serialized
     * @return mixed
     */
    public function deserialize(array $serialized);
}
