<?php namespace EventSourcing\Test\Serialization;

use Carbon\Carbon;
use EventSourcing\Domain\DomainEvent;
use EventSourcing\EventDispatcher\MetaData;
use EventSourcing\EventDispatcher\TransferObject;
use EventSourcing\Serialization\Deserializer;
use EventSourcing\Serialization\Serializer;
use EventSourcing\Test\TestCase;

class SerializationTest extends TestCase
{
    use Serializer, Deserializer;

    /**
     * @test
     */
    public function it_can_serialize_and_deserialize()
    {
        $data = new SerializationExampleClass(new D(new E()));

        $serialized = $this->serialize($data);

        $this->assertEquals($data, $this->deserialize($serialized));
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

class E
{
    private $id = 321;
}
