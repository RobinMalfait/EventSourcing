<?php namespace EventSourcing\Laravel\Commands;

use EventSourcing\EventDispatcher\EventDispatcher;
use EventSourcing\Serialization\Deserializer;
use Illuminate\Console\Command;

class RebuildProjectionsCommand extends Command
{
    use Deserializer;

    /**
     * @var
     */
    private $app;

    private $steps = 1;

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
        $this->dispatcher = $this->app->make(EventDispatcher::class);
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->stepTitle("Reset all migrations");
        $this->call("migrate:reset");

        $this->stepTitle("Migrate all migrations");
        $this->call("migrate");

        $this->stepTitle("Loading events from EventStore");
        $events = $this->getAllEvents();

        $this->output->progressStart(count($events));

        foreach ($events as $event) {
            $event = $this->deserialize(json_decode($event->payload, true));

            $this->dispatcher->project($event);

            $this->output->progressAdvance();
        }

        $this->output->progressFinish();
    }

    private function getAllEvents()
    {
        return $this->app['db']
                ->connection('eventstore')
                ->table('eventstore')
                ->select(['uuid', 'version', 'payload', 'type', 'recorded_on'])
                ->get();
    }

    private function stepTitle($string, $fillWith = "=")
    {
        $comment = [
            "<comment>",
            "</comment>",
        ];
        $data = $comment[0] . "Step " . $this->steps . ")" . $comment[1] . " " . $string;

        $this->info(PHP_EOL . $data . PHP_EOL . "<comment>" . str_repeat($fillWith, strlen($data) - strlen(implode("", $comment))) . PHP_EOL);

        $this->steps++;
    }
}
