<?php namespace EventSourcing\Test\EventDispatcher;

use EventSourcing\Domain\DomainEvent;
use EventSourcing\Domain\MetaData;
use EventSourcing\EventDispatcher\EventSourcingEventDispatcher;
use EventSourcing\EventDispatcher\Listener;
use EventSourcing\Test\TestCase;

class EventDispatcherTest extends TestCase
{
    /**
     * @var
     */
    private $dispatcher;

    /**
     * @var array
     */
    private $listeners = [];

    /**
     *
     */
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

        $this->dispatcher->dispatch(new DomainEventStub(), new MetaData([]));

        $this->assertTrue($this->listeners[0]->isCalled());
        $this->assertFalse($this->listeners[1]->isCalled());
    }

    /**
     * @test
     */
    public function it_can_register_multiple_listeners()
    {
        $this->dispatcher->addListeners(DomainEventStub::class, $this->listeners);

        $this->dispatcher->dispatch(new DomainEventStub(), new MetaData([]));

        $this->assertTrue($this->listeners[0]->isCalled());
        $this->assertTrue($this->listeners[1]->isCalled());
    }
}

/**
 * Class ListenerStub
 * @package EventSourcing\Test\EventDispatcher
 */
class ListenerStub implements Listener
{
    /**
     * @var bool
     */
    private $isCalled = false;

    /**
     * @param DomainEvent $event
     * @param array $metadata
     */
    public function handle(DomainEvent $event, $metadata = [])
    {
        $this->isCalled = true;
    }

    /**
     * @return bool
     */
    public function isCalled()
    {
        return $this->isCalled;
    }
}

/**
 * Class DomainEventStub
 * @package EventSourcing\Test\EventDispatcher
 */
class DomainEventStub implements DomainEvent
{
    /**
     * @return int
     */
    public function getAggregateId()
    {
        return 123;
    }

    /**
     * @return array
     */
    public function serialize()
    {
        return [];
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
