<?php namespace EventSourcing\EventSourcing;

use Rhumsaa\Uuid\Uuid as RhumsaaUuid;

final class Uuid
{
    /**
     * Generate a version 4 (random) UUID.
     *
     * @return string
     */
    public static function generate()
    {
        return (string) RhumsaaUuid::uuid4();
    }
}
