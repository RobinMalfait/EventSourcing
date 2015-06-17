<?php namespace EventSourcing\Laravel\Commands;

use EventSourcing\Serialization\Deserializer;
use Illuminate\Console\Command;

class RebuildProjectionsCommand extends Command
{
    use Deserializer;

    /**
     * @var
     */
    private $app;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "event-sourcing:rebuild-projections";

    /**
     * The console command description
     *
     * @var string
     */
    protected $description = 'Rebuild all the projections by cleaning migrations and replaying events.';

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
        $this->info($this->spacer() . " Reset and re-run all migrations " . $this->spacer());
        $this->call("migrate:refresh");

        $this->info($this->spacer() . " Loading events from EventStore " . $this->spacer());
        $events = $this->getAllEvents();

        $this->output->progressStart(count($events));

        foreach ($events as $event) {
            $event = $this->deserialize(json_decode($event->payload, true));

            var_dump($event);

            $this->output->progressAdvance();
        }

        $this->output->progressFinish();
    }

    private function spacer()
    {
        return str_repeat("=", 10);
    }

    private function getAllEvents()
    {
        return $this->app['db']
                ->connection('eventstore')
                ->table('eventstore')
                ->select(['uuid', 'version', 'payload', 'type', 'recorded_on'])
                ->orderBy('version', 'asc')
                ->get();
    }
}
