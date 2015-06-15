<?php namespace EventSourcing\Laravel\Commands;

use Illuminate\Console\Command;

abstract class EventSourcingCommand extends Command
{
    private $namespace = "event-sourcing";

    protected function setCommandName($name)
    {
        $this->signature = $this->namespace . ':' . $name;
    }

    protected function setCommandDescription($description)
    {
        $this->description = $description;
    }

}