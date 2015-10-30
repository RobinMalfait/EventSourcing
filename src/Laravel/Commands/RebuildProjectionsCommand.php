<?php namespace EventSourcing\Laravel\Commands;

use Carbon\Carbon;
use EventSourcing\EventDispatcher\EventDispatcher;
use EventSourcing\EventStore\EventStore;
use EventSourcing\Serialization\Deserializer;
use Exception;
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
     * @var EventStore
     */
    private $eventstore;

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

    /**
     * @param $app
     */
    public function __construct($app)
    {
        parent::__construct();

        $this->app = $app;
        $this->config = $this->app->make(Config::class);
        $this->dispatcher = $this->app->make(EventDispatcher::class);
        $this->eventstore = $this->app->make(EventStore::class);
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->timeMe(function () {
            $this->runPreRebuildCommands();

            $this->dispatcher->rebuildMode($this->config->get('event_sourcing.disable_projection_queue', true));

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

            $this->dispatcher->rebuildMode(false);

            $this->runPostRebuildCommands();
        }, "Total Execution Time");
    }

    /**
     * @return mixed
     */
    private function getAllEvents()
    {
        return $this->eventstore->getAllEvents();
    }

    /**
     * @param $string
     * @param string $fillWith
     */
    private function printHeader($string, $fillWith = "=")
    {
        $comment = collect(["<comment>", "</comment>"]);

        $data = $comment->first() . "Step " . $this->steps . ")" . $comment->last() . " " . $string;

        $this->info(PHP_EOL . $data . PHP_EOL . "<comment>" . str_repeat($fillWith, strlen($data) - strlen(implode("", $comment->all()))) . PHP_EOL);

        $this->steps++;
    }

    /**
     * @param $title
     * @param callable $method
     */
    private function action($title, callable $method)
    {
        $this->printHeader($title);
        $this->timeMe($method);
    }

    /**
     * @param $timeInSeconds
     * @return string
     */
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

    /**
     *
     */
    private function runPreRebuildCommands()
    {
        $this->runListOfCommands(
            $this->config->get('event_sourcing.pre_rebuild', [])
        );
    }

    /**
     *
     */
    private function runPostRebuildCommands()
    {
        $this->runListOfCommands(
            $this->config->get('event_sourcing.post_rebuild', [])
        );
    }

    /**
     * @param $commands
     */
    private function runListOfCommands($commands)
    {
        foreach ($commands as $command => $title) {
            $this->action(
                $title,
                function () use ($command) {
                    list($command, $options) = $this->parseCommand($command);
                    $this->call($command, $options);
                }
            );
        }
    }

    /**
     * @param $command
     * @return array
     */
    private function parseCommand($command)
    {
        $items = collect(explode(" ", $command));
        $command = $items->shift();

        $options = [];

        foreach ($items as $item) {
            try {
                list($key, $value) = explode('=', $item);
            } catch (Exception $e) {
                $key = $item;
                $value = true; // Boolean
            }

            $options[$key] = $value;
        }

        return [$command, $options];
    }

    /**
     * @param callable $callback
     * @param string $message
     */
    private function timeMe(callable $callback, $message = "Excecution Time")
    {
        $start = microtime(true);
        call_user_func($callback);
        $end = microtime(true);

        $executionTime = $this->calcExecutionTime($end - $start);
        $this->info(PHP_EOL . $message . ": <comment>" . $executionTime . "</comment>");
    }
}
