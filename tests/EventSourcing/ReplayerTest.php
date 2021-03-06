<?php namespace EventSourcing\Test\EventSourcing;

use EventSourcing\Domain\AggregateRoot;
use EventSourcing\Domain\DomainEvent;
use EventSourcing\Test\TestCase;

class ReplayerTest extends TestCase
{
    private $aggregate;

    public function setUp()
    {
        $this->aggregate = new ReplayerStub();

        $this->events[] = new DomainEventStub();
    }

    /**
     * @test
     */
    public function the_version_should_be_negative_at_default()
    {
        $this->assertEquals(-1, $this->aggregate->getVersion());
        $this->assertFalse($this->aggregate->isCalled());
    }

    /**
     * @test
     */
    public function the_apply_method_should_be_called()
    {
        $aggregate = ReplayerStub::replayEvents($this->events);

        $this->assertEquals(0, $aggregate->getVersion());
        $this->assertTrue($aggregate->isCalled());
    }
}

class ReplayerStub extends AggregateRoot
{
    private $called = false;

    public function isCalled()
    {
        return $this->called;
    }

    public function applyDomainEventStub(DomainEventStub $event)
    {
        $this->called = true;
    }
}

class DomainEventStub implements DomainEvent
{
    public function getAggregateId()
    {
        return 123;
    }

    /**
     * @return array
     */
    public function serialize()
    {
        // TODO: Implement serialize() method.
    }

    /**
     * @param array $data
     * @return mixed
     */
    public static function deserialize(array $data)
    {
        // TODO: Implement deserialize() method.
    }

    /**
     * @return array
     */
    public function getMetaData()
    {
        return [];
    }
}
