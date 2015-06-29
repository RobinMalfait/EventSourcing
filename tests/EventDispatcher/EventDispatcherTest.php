<?php namespace EventSourcing\Test\EventDispatcher;

use EventSourcing\Domain\DomainEvent;
use EventSourcing\EventDispatcher\EventSourcingEventDispatcher;
use EventSourcing\EventDispatcher\Listener;
use EventSourcing\Test\TestCase;

class EventDispatcherTest extends TestCase
{
    private $dispatcher;

    private $listeners = [];

    public function setUp()
    {
        $this->dispatcher = new EventSourcingEventDispatcher();

        $this->listeners[] = new ListenerStub();
        $this->listeners[] = new ListenerStub();
    }

    /**
     * @test
     */
    public function it_calls_the_subscribed_listeners()
    {
        $this->dispatcher->addListener(DomainEventStub::class, $this->listeners[0]);

        $this->dispatcher->dispatch(new DomainEventStub());

        $this->assertTrue($this->listeners[0]->isCalled());
        $this->assertFalse($this->listeners[1]->isCalled());
    }

    /**
     * @test
     */
    public function it_can_register_multiple_listeners()
    {
        $this->dispatcher->addListeners(DomainEventStub::class, $this->listeners);

        $this->dispatcher->dispatch(new DomainEventStub());

        $this->assertTrue($this->listeners[0]->isCalled());
        $this->assertTrue($this->listeners[1]->isCalled());
    }
}

class ListenerStub implements Listener
{
    private $isCalled = false;

    public function handle(DomainEvent $event, $metadata = [])
    {
        $this->isCalled = true;
    }

    public function isCalled()
    {
        return $this->isCalled;
    }
}

class DomainEventStub implements DomainEvent
{
    public function getAggregateId()
    {
        return 123;
    }
}
