<?php namespace EventSourcing\Serialization; 

use ReflectionClass;

trait Deserializer
{
    public function deserialize($data)
    {
        $eventClass = array_keys($data)[0];

        return $this->deserializeRecursively($eventClass, $data[$eventClass]);
    }

    private function deserializeRecursively($class, $data)
    {
        $reflectionClass = new ReflectionClass($class);
        $obj = $reflectionClass->newInstanceWithoutConstructor();

        foreach ($reflectionClass->getProperties() as $property) {
            $property->setAccessible(true);
            $propertyName = $property->getName();

            $contents = $data[$propertyName];

            if (isset($contents['type'])) {
                $property->setValue($obj,
                    $this->deserializeRecursively(
                        $contents['type'],
                        $contents['value']
                    )
                );
            } else {
                $property->setValue($obj, $data[$propertyName]);
            }
        }

        return $obj;
    }
}
