<?php namespace EventSourcing\Domain;

use EventSourcing\EventDispatcher\EventGenerator;
use EventSourcing\EventSourcing\Replayer;

abstract class AggregateRoot
{
    use EventGenerator, Replayer;
}
