<?php namespace EventSourcing\Laravel\Commands;

use Carbon\Carbon;
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
        $start = microtime(true);

        $this->printHeader("Application is going down");
        $this->call("down");

        $this->printHeader("Reset all migrations");
        $this->call("migrate:reset");

        $this->printHeader("Migrate all migrations");
        $this->call("migrate");

        $this->printHeader("Loading events from EventStore");
        $events = $this->getAllEvents();

        $this->output->progressStart(count($events));

        foreach ($events as $event) {
            $metadata = [
                'uuid' => $event->uuid,
                'version' => $event->version,
                'type' => $event->type,
                'recorded_on' => new Carbon($event->recorded_on)
            ];

            $event = $this->deserialize(json_decode($event->payload, true));
            $this->dispatcher->project($event, $metadata);
            $this->output->progressAdvance();
        }

        $this->output->progressFinish();

        $this->printHeader("Application is going back up");
        $this->call("up");

        $this->printHeader("Statistics");

        $end = microtime(true);

        $executionTime = $this->calcExecutionTime($end - $start);

        $this->info("Excecution time: <comment>" . $executionTime . "</comment>");
    }

    private function getAllEvents()
    {
        return $this->app['db']
            ->connection('eventstore')
            ->table('eventstore')
            ->select(['uuid', 'version', 'payload', 'type', 'recorded_on'])
            ->get();
    }

    private function printHeader($string, $fillWith = "=")
    {
        $comment = collect(["<comment>", "</comment>"]);

        $data = $comment->first() . "Step " . $this->steps . ")" . $comment->last() . " " . $string;

        $this->info(PHP_EOL . $data . PHP_EOL . "<comment>" . str_repeat($fillWith, strlen($data) - strlen(implode("", $comment->all()))) . PHP_EOL);

        $this->steps++;
    }

    private function calcExecutionTime($timeInSeconds)
    {
        $time = $timeInSeconds;

        if ($time > 59) {
            $time /= 60;
            $symbol = "m";
        } elseif ($time > 0.99) {
            $symbol = "s";
        } else {
            $time *= 1000;
            $symbol = "ms";
        }

        return round($time, 1) . $symbol;
    }
}
