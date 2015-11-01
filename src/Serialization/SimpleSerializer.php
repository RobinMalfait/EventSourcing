<?php namespace EventSourcing\Serialization;

class SimpleSerializer implements Serializer
{
    /**
     * @param $object
     * @return array
     */
    public function serialize(Serializable $object)
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
    public function deserialize(array $serialized)
    {
        return $serialized['class']::deserialize($serialized['payload']);
    }
}
