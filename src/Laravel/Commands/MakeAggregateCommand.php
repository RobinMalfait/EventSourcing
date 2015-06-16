<?php namespace EventSourcing\Laravel\Commands;

use Illuminate\Console\Command;

class MakeAggregateCommand extends Command
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
    protected $signature = "event-sourcing:make:aggregate {aggregate}";

    /**
     * The console command description
     *
     * @var string
     */
    protected $description = 'Make an aggregate.';

    public function __construct($app)
    {
        parent::__construct();

        $this->app = $app;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $fqdn = $this->argument('aggregate');
        $parts = explode("\\", $fqdn);

        $aggregate = end($parts);

        unset($parts[count($parts) - 1]);

        $folder = implode("/", $parts);

        mkdir($folder, 0777, true);

        file_put_contents($folder . '/' . $aggregate . '.php',
            $this->compiler->compile(file_get_contents(__DIR__ . '../_templates/aggregate.txt'), [
                'NAMESPACE' => implode('/', $parts),
                'AGGREGATE' => $aggregate
            ])
        );
    }
}
