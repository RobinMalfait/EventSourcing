<?php namespace EventSourcing\Test\EventDispatcher;

use EventSourcing\Domain\DomainEvent;
use EventSourcing\EventDispatcher\EventGenerator;
use EventSourcing\Test\TestCase;

class EventGeneratorTest extends TestCase
{
    private $stub;

    public function setUp()
    {
        $this->stub = new EventGeneratorStub();
    }

    /**
     * @test
     */
    public function it_has_no_events_initially()
    {
        $this->assertCount(0, $this->stub->releaseEvents());
    }

    /**
     * @test
     */
    public function it_has_one_event_after_applying()
    {
        $event = new EventGeneratorExampleEventStub();
        $this->stub->apply($event);

        $this->assertCount(1, $this->stub->releaseEvents());
    }

    /**
     * @test
     */
    public function it_can_receive_the_right_event()
    {
        $event = new EventGeneratorExampleEventStub();
        $this->stub->apply($event);

        $events = $this->stub->releaseEvents();

        $this->assertCount(1, $events);
        $this->assertSame($event, $events[0]);
    }

    /**
     * @test
     */
    public function it_cleans_released_events()
    {
        $event = new EventGeneratorExampleEventStub();
        $this->stub->apply($event);

        $this->assertCount(1, $this->stub->releaseEvents());

        // Has been cleaned
        $this->assertCount(0, $this->stub->releaseEvents());
    }
}

class EventGeneratorStub
{
    use EventGenerator;
}

class EventGeneratorExampleEventStub implements DomainEvent
{
    public function getAggregateId()
    {
        return 123;
    }
}
