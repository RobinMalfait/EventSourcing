<?php namespace EventSourcing\Test\Serialization;

use EventSourcing\Serialization\Deserializer;
use EventSourcing\Serialization\Serializable;
use EventSourcing\Serialization\Serializer;
use EventSourcing\Serialization\SimpleSerializer;
use EventSourcing\Test\TestCase;

class SerializationTest extends TestCase
{
    /**
     * @var Serializer
     */
    private $serializer;

    public function setUp()
    {
        $this->serializer = new SimpleSerializer();
    }

    /**
     * @test
     */
    public function it_can_serialize_itself()
    {
        $object = new SerializationExampleClass('a', 'b');

        $this->assertEquals(['foo' => 'a', 'bar' => 'b'], $object->serialize());
    }

    /**
     * @test
     */
    public function it_can_deserialize_itself()
    {
        $object = new SerializationExampleClass('a', 'b');

        $this->assertInstanceOf(SerializationExampleClass::class, $object->deserialize(['foo' => 'a', 'bar' => 'b']));
    }

    /**
     * @test
     */
    public function it_can_be_serialized_and_deserialized()
    {
        $object = new SerializationExampleClass('a', 'b');

        $serialized = $this->serializer->serialize($object);

        $this->assertArrayHasKey('class', $serialized);
        $this->assertArrayHasKey('payload', $serialized);

        $this->assertEquals($object, $this->serializer->deserialize($serialized));
    }
}

class SerializationExampleClass implements Serializable
{
    public $foo;

    public $bar;

    public function __construct($foo, $bar)
    {
        $this->foo = $foo;
        $this->bar = $bar;
    }

    /**
     * @return array
     */
    public function serialize()
    {
        return [
            'foo' => $this->foo,
            'bar' => $this->bar
        ];
    }

    /**
     * @param array $data
     * @return mixed
     */
    public static function deserialize(array $data)
    {
        return new static(
            $data['foo'],
            $data['bar']
        );
    }
}
