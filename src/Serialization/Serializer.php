<?php namespace EventSourcing\Serialization;

class Serializer
{
    /**
     * @param $object
     * @return array
     */
    public static function serialize(Serializable $object)
    {
        return [
            'class' => get_class($object),
            'payload' => $object->serialize()
        ];
    }

    /**
     * @param array $serialized
     * @return mixed
     */
    public static function deserialize(array $serialized)
    {
        return $serialized['class']::deserialize($serialized['payload']);
    }
}
