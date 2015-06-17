<?php namespace EventSourcing\Laravel\Commands;

use Illuminate\Console\Command;

class MakeAggregateCommandCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "event-sourcing:make-command";

    /**
     * The console command description
     *
     * @var string
     */
    protected $description = 'Make a command for an aggregate.';

    public function handle()
    {
        $aggregate = $this->ask("What is the aggregate name");
        $command = $this->ask("What is the command name");
        $commandHandler = $command . "Handler";

        $namespace = $this->ask("What is the namespace", $this->parseName(str_plural($aggregate) . "/Commands"));

        $commandPath = $this->getPath($this->parseName($namespace) . "/" . $command);
        $commandHandlerPath = $this->getPath($this->parseName($namespace) . "/" . $commandHandler);

        $commandStub = file_get_contents(__DIR__ . "/../stubs/command.stub");
        $commandStub = $this->replaceNamespace($commandStub, $namespace)->replaceClass($commandStub, $commandStub);

        $commandHandlerStub = file_get_contents(__DIR__ . "/../stubs/commandHandler.stub");
        $commandHandlerStub = str_replace("Aggregate", $aggregate, $this->replaceNamespace($commandHandlerStub, $namespace)->replaceClass($commandHandlerStub, $commandStub));

        file_put_contents($commandPath, $commandStub);
        file_put_contents($commandHandlerPath, $commandHandlerStub);

        $this->info("Command <comment>" . $command . "</comment> has been created in <comment>(" . $commandPath . ")</comment>");
        $this->info("Command Handler <comment>" . $commandHandler . "</comment> has been created in <comment>(" . $commandHandlerPath . ")</comment>");
    }

    /**
     * Get the destination class path.
     *
     * @param  string  $name
     * @return string
     */
    protected function getPath($name)
    {
        $name = str_replace($this->laravel->getNamespace(), '', $name);

        return $this->laravel['path'] . '/' . str_replace('\\', '/', $name) . '.php';
    }

    /**
     * Parse the name and format according to the root namespace.
     *
     * @param  string  $name
     * @return string
     */
    protected function parseName($name)
    {
        $rootNamespace = $this->laravel->getNamespace();

        if (starts_with($name, $rootNamespace)) {
            return $name;
        }

        if (str_contains($name, '/')) {
            $name = str_replace('/', '\\', $name);
        }

        return $this->parseName($this->getDefaultNamespace(trim($rootNamespace, '\\')) . '\\' . $name);
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace;
    }

    /**
     * Build the directory for the class if necessary.
     *
     * @param  string  $path
     * @return string
     */
    protected function makeDirectory($path)
    {
        if (! is_dir(dirname($path))) {
            mkdir(dirname($path), 0777, true);
        }
    }

    /**
     * Build the class with the given name.
     *
     * @param  string  $name
     * @return string
     */
    protected function buildClass($name)
    {
        $stub = file_get_contents($this->getStub());

        return $this->replaceNamespace($stub, $name)->replaceClass($stub, $name);
    }

    /**
     * Replace the namespace for the given stub.
     *
     * @param  string  $stub
     * @param  string  $name
     * @return $this
     */
    protected function replaceNamespace(&$stub, $name)
    {
        $stub = str_replace(
            'DummyNamespace', $this->getNamespace($name), $stub
        );

        $stub = str_replace(
            'DummyRootNamespace', $this->laravel->getNamespace(), $stub
        );

        return $this;
    }

    /**
     * Get the full namespace name for a given class.
     *
     * @param  string  $name
     * @return string
     */
    protected function getNamespace($name)
    {
        return trim(implode('\\', array_slice(explode('\\', $name), 0, -1)), '\\');
    }

    /**
     * Replace the class name for the given stub.
     *
     * @param  string  $stub
     * @param  string  $name
     * @return string
     */
    protected function replaceClass($stub, $name)
    {
        $class = str_replace($this->getNamespace($name) . '\\', '', $name);

        return str_replace('DummyClass', $class, $stub);
    }
}
