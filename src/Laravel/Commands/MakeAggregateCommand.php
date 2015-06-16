<?php namespace EventSourcing\Laravel\Commands;

use Illuminate\Console\GeneratorCommand;

class MakeAggregateCommand extends GeneratorCommand
{
    /**
     * The Schema Builder
     *
     * @var \Illuminate\Contracts\Foundation\Application
     */
    private $app;

    private $compiler;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "event-sourcing:make:aggregate";

    /**
     * The console command description
     *
     * @var string
     */
    protected $description = 'Make an aggregate.';

    /**
     * @var string
     */
    protected $type = "Aggregate";

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__ . '/stubs/aggregate.stub';
    }
}
