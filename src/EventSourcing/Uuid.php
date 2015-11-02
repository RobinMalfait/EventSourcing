<?php namespace EventSourcing\EventSourcing;

/**
 * Class Uuid
 * @package EventSourcing\EventSourcing
 */
final class Uuid
{
    /**
     * Generate a version 4 (random) UUID.
     *
     * @return string
     */
    public static function generate()
    {
        return (string) \Rhumsaa\Uuid\Uuid::uuid4();
    }
}
