<?php namespace EventSourcing\Testing;

use Exception;
use PHPUnit_Framework_TestCase;

abstract class TestHelper extends PHPUnit_Framework_TestCase
{
    /**
     * @param Exception $exception
     */
    protected function throws(Exception $exception)
    {
        $this->assertInstanceOf(get_class($exception), $this->caughtException);
    }
}
