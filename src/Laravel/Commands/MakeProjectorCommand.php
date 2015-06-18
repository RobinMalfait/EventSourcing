<?php namespace EventSourcing\Laravel\Commands;

class MakeProjectorCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "event-sourcing:make-projector {name}";

    /**
     * The console command description
     *
     * @var string
     */
    protected $description = 'Make a projector.';

    /**
     * @var string
     */
    protected $type = "Projector";

    /**
     * Filename Suffix
     *
     * @var string
     */
    protected $fileSuffix = "Projector";

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__ . '/../stubs/projector.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\\' . str_plural($this->argument('name')) . '\\Projectors';
    }
}
