<?php namespace EventSourcing\Laravel\Commands;

class MakeServiceProviderCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "event-sourcing:make-service-provider {name}";

    /**
     * The console command description
     *
     * @var string
     */
    protected $description = 'Make a service provider.';

    /**
     * @var string
     */
    protected $type = "Service Provider";

    /**
     * Filename Suffix
     *
     * @var string
     */
    protected $fileSuffix = "ServiceProvider";

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__ . '/../stubs/serviceProvider.stub';
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
