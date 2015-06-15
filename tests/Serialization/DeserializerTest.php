<?php namespace EventSourcing\Test\Serialization;

use EventSourcing\Serialization\Deserializer;
use EventSourcing\Test\TestCase;

class DeserializerTest extends TestCase
{
    private $deserializerStub;

    /**
     * @test
     */
    public function setUp()
    {
        $this->deserializerStub = new DeserializerStub();
    }

    /**
     * @test
     */
    public function it_can_serialize_a_class()
    {
        $result = $this->deserializerStub->deserialize([
            DeserializerClass::class => [
                'id' => 12,
                'name' => [
                    'type' => DeserializerName::class,
                    'value' => [
                        'firstname' => 'John',
                        'lastname' => 'Doe'
                    ]
                ]
            ]
        ]);

        $expected = new DeserializerClass(new DeserializerName("John", "Doe"));

        $this->assertEquals($expected, $result);
    }
}

class DeserializerStub
{
    use Deserializer;
}

class DeserializerClass {

    private $id = 12; // Primitives

    private $name; // Other Classes

    public function __construct(DeserializerName $name)
    {
        $this->name = $name;
    }
}

class DeserializerName
{
    private $firstname;

    private $lastname;

    function __construct($firstname, $lastname)
    {
        $this->firstname = $firstname;
        $this->lastname = $lastname;
    }
}
