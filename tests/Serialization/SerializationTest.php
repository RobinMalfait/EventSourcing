<?php namespace EventSourcing\Test\Serialization;

use EventSourcing\Serialization\Deserializer;
use EventSourcing\Serialization\Serializer;

class SerializationTest
{
    use Serializer, Deserializer;

    /**
     * @test
     */
    public function it_can_serialize_and_deserialize()
    {
        $serialized = $this->serialize(new SerializationExampleClass(new D(new E())));

        $this->assertEquals($serialized, $this->deserialize($serialized));
    }
}


class SerializationExampleClass
{
    public $a = "Some String";

    protected $b = "Some Other String";

    private $c = "Another string but private";

    private $bool = true;

    private $id = 123;

    private $d;

    public function __construct(D $d)
    {
        $this->d = $d;
    }
}

class D
{
    private $id = 321;

    private $recursive;

    public function __construct(E $e)
    {
        $this->recursive = $e;
    }
}
