<?php namespace EventSourcing\Laravel\Commands;

use Carbon\Carbon;
use EventSourcing\EventDispatcher\EventDispatcher;
use EventSourcing\Serialization\Deserializer;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\Application;
use Illuminate\Contracts\Config\Repository as Config;

class RebuildProjectionsCommand extends Command
{
    use Deserializer;

    /**
     * @var Application
     */
    private $app;

    /**
     * @var int
     */
    private $steps = 1;

    /**
     * @var Config
     */
    private $config;

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
        $this->config = $this->app->make('config');
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

        $this->runPreRebuildCommands();

        $this->action("Loading events from EventStore", function () {
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
        });

        $this->runPostRebuildCommands();

        $this->printHeader("Statistics");
        $end = microtime(true);
        $executionTime = $this->calcExecutionTime($end - $start);
        $this->info("Total Excecution time: <comment>" . $executionTime . "</comment>");
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

    private function action($title, callable $method)
    {
        $this->printHeader($title);
        $start = microtime(true);
        call_user_func($method);
        $end = microtime(true);

        $executionTime = $this->calcExecutionTime($end - $start);
        $this->info("Excecution time: <comment>" . $executionTime . "</comment>");
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

    private function runPreRebuildCommands()
    {
        foreach ($this->config->get('event_sourcing.pre_rebuild') as $command => $title) {
            $this->action(
                $title,
                function () use ($command) {
                    $this->call($command);
                }
            );
        }
    }

    private function runPostRebuildCommands()
    {
        foreach ($this->config->get('event_sourcing.post_rebuild') as $command => $title) {
            $this->action(
                $title,
                function () use ($command) {
                    $this->call($command);
                }
            );
        }
    }
}
