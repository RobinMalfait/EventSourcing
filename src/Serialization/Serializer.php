<?php namespace EventSourcing\Serialization;

use ReflectionClass;

trait Serializer
{
    public function serialize($event)
    {
        $data = $this->serializeRecursively($event);

        return [get_class($event) => $data];
    }

    private function serializeRecursively($class)
    {
        $data = [];
        $properties = (new ReflectionClass($class))->getProperties();

        foreach ($properties as $property) {
            $property->setAccessible(true);
            $value = $property->getValue($class);

            if (is_object($value)) {
                $data[$property->getName()] = [
                    'type' => get_class($value),
                    'value' => $this->serializeRecursively($value)
                ];
            } else {
                $data[$property->getName()] = $value;
            }
        }

        return $data;
    }
}
