<?php namespace EventSourcing\Test\EventSourcing;

use EventSourcing\Domain\AggregateRoot;
use EventSourcing\Domain\DomainEvent;
use EventSourcing\EventSourcing\Replayer;
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
    public function the_playhead_should_be_negative_at_default()
    {
        $this->assertEquals(-1, $this->aggregate->playhead);
        $this->assertFalse($this->aggregate->isCalled());
    }

    /**
     * @test
     */
    public function the_apply_method_should_be_called()
    {
        $aggregate = ReplayerStub::replayEvents($this->events);

        $this->assertEquals(0, $aggregate->playhead);
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
}
