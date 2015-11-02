<?php namespace EventSourcing\Domain;

use EventSourcing\EventDispatcher\EventGenerator;
use EventSourcing\EventSourcing\Replayer;

/**
 * Class AggregateRoot
 * @package EventSourcing\Domain
 */
abstract class AggregateRoot
{
    use EventGenerator, Replayer;
}
