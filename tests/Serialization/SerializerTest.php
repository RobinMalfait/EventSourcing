<?php namespace EventSourcing\Test\Serialization;

use EventSourcing\Serialization\Serializer;
use EventSourcing\Test\TestCase;

class SerializerTest extends TestCase
{
    private $serializerStub;

    /**
     * @test
     */
    public function setUp()
    {
        $this->serializerStub = new SerializerStub();
    }

    /**
     * @test
     */
    public function it_can_serialize_a_class()
    {
        $result = $this->serializerStub->serialize(new SerializerClass(new SerializerName("John", "Doe")));

        $expected = [
            SerializerClass::class => [
                'id' => 12,
                'name' => [
                    'type' => SerializerName::class,
                    'value' => [
                        'firstname' => 'John',
                        'lastname' => 'Doe'
                    ]
                ]
            ]
        ];

        $this->assertEquals($expected, $result);
    }
}

class SerializerStub
{
    use Serializer;
}

class SerializerClass {

    private $id = 12; // Primitives

    private $name; // Other Classes

    public function __construct(SerializerName $name)
    {
        $this->name = $name;
    }
}

class SerializerName
{
    private $firstname;

    private $lastname;

    function __construct($firstname, $lastname)
    {
        $this->firstname = $firstname;
        $this->lastname = $lastname;
    }
}
