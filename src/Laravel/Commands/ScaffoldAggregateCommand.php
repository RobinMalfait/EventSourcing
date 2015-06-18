<?php namespace EventSourcing\Laravel\Commands;

use Illuminate\Console\Command;

class ScaffoldAggregateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "event-sourcing:scaffold {name}";

    /**
     * The console command description
     *
     * @var string
     */
    protected $description = 'Scaffolding everything you need!';

    /**
     * @var
     */
    protected $app;

    public function __construct($app)
    {
        parent::__construct();

        $this->app = $app;
    }

    /**
     *
     */
    public function handle()
    {
        $name = $this->argument('name');

        $this->callSilent('event-sourcing:make-aggregate', ['name' => $name]);
        $this->callSilent('event-sourcing:make-aggregate-repository', ['name' => $name]);

        $folders = [
            'Commands',
            'Events',
            'Exceptions',
            'Listeners',
            'Projectors'
        ];

        $this->makeFolders($folders);

        $this->callSilent('event-sourcing:make-service-provider', ['name' => $name]);
        $this->callSilent('event-sourcing:make-projector', ['name' => $name]);

        $this->info("Scaffolding $name successful!");
    }

    private function makeFolders($folders)
    {
        foreach ($folders as $folder) {
            $folder = $this->parseName(str_plural($this->argument('name')) . '/' . $folder);

            $name = str_replace($this->laravel->getNamespace(), '', $folder);

            mkdir($this->laravel['path'] . '/' . str_replace('\\', '/', $name), 0777, true);
        }
    }

    /**
     * Parse the name and format according to the root namespace.
     *
     * @param  string  $name
     * @return string
     */
    private function parseName($name)
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
}
