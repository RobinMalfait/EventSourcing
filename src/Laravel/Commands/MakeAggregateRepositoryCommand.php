<?php namespace EventSourcing\Laravel\Commands;

class MakeAggregateRepositoryCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "event-sourcing:make-aggregate-repository {name}";

    /**
     * The console command description
     *
     * @var string
     */
    protected $description = 'Make an aggregate repository.';

    /**
     * @var string
     */
    protected $type = "Aggregate Repository";

    /**
     * Filename Suffix
     *
     * @var string
     */
    protected $fileSuffix = "Repository";

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__ . '/../stubs/repository.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\\' . str_plural($this->argument('name'));
    }
}
