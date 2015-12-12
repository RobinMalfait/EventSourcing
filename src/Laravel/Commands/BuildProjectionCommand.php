<?php namespace EventSourcing\Laravel\Commands;

use EventSourcing\Domain\MetaData;
use EventSourcing\EventDispatcher\EventDispatcher;
use EventSourcing\EventDispatcher\Projection;
use EventSourcing\EventStore\EventStore;
use EventSourcing\Serialization\Serializer;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Support\Collection;

class BuildProjectionCommand extends Command
{
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
    protected $signature = "event-sourcing:build-projection {projector?}";

    /**
     * The console command description
     *
     * @var string
     */
    protected $description = 'Build a projection';

    /**
     * @var int
     */
    private $totalExecutionTime = 0;

    /**
     *
     */
    public function __construct()
    {
        parent::__construct();

        $this->config = app()->make(Config::class);
        $this->dispatcher = app()->make(EventDispatcher::class);
        $this->eventstore = app()->make(EventStore::class);
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->runPreBuildCommands();

        $this->setRebuildModeTo(true);
        $this->rebuildEvents();
        $this->setRebuildModeTo(false);

        $this->runPostBuildCommands();
    }

    /**
     * @param Collection $types
     * @return mixed
     */
    private function getAllEvents(Collection $types)
    {
        $events = collect($this->eventstore->getAllEvents());

        return $events->filter(function ($event) use ($types) {
            return $types->contains($event->type);
        });
    }

    /**
     * @param $string
     * @internal param string $fillWith
     */
    private function printHeader($string)
    {
        $data = "<comment>Step " . $this->steps . ")</comment> " . $string;

        $this->info(PHP_EOL . $data . PHP_EOL);

        $this->steps++;
    }

    /**
     * @param callable $callback
     * @return string
     */
    private function timeExecution(callable $callback)
    {
        $start = microtime(true);
        call_user_func($callback);
        $end = microtime(true);

        $diff = $end - $start;

        $this->totalExecutionTime += $diff;

        return $diff;
    }

    /**
     * @param $title
     * @param callable $method
     */
    private function action($title, callable $method)
    {
        $this->printHeader($title);
        $time = $this->timeExecution($method);

        $this->info(PHP_EOL . "Execution Time: <comment>" . $this->humanReadableExecutionTime($time)) . '</comment>';
    }

    /**
     *
     */
    private function runPreBuildCommands()
    {
        $this->runListOfCommands(
            $this->config->get('event_sourcing.pre_build', [])
        );
    }

    /**
     *
     */
    private function runPostBuildCommands()
    {
        $this->runListOfCommands(
            $this->config->get('event_sourcing.post_build', [])
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

    private function setRebuildModeTo($value)
    {
        if (! $value) {
            $this->dispatcher->rebuildMode(false);
        }

        $this->dispatcher->rebuildMode(
            (bool) $this->config->get('event_sourcing.disable_projection_queue')
        );
    }

    private function rebuildEvents()
    {
        try {
            $projector = app()->make($this->argument('projector') ?: $this->ask('Which projector do you want (Namespace + Class)'));

            if ( ! $projector instanceof Projection) {
                throw new Exception("This is not a valid Projector, a Projector must implement the projection interface.");
            }

            $eventsNeeded = collect($projector->needsDomainEvents())->map(function ($event) {
                return strtolower(str_replace("\\", ".", $event));
            });

            $this->action("Loading events from EventStore", function () use ($eventsNeeded) {
                $events = $this->getAllEvents($eventsNeeded);

                $this->output->progressStart(count($events));

                foreach ($events as $event) {
                    $myMetaData = Serializer::deserialize(json_decode($event->metadata, true));
                    $systemMetaData = new MetaData([
                        'uuid' => $event->uuid,
                        'version' => $event->version,
                        'type' => $event->type,
                        'recorded_on' => $event->recorded_on
                    ]);

                    $allMetaData = $myMetaData->merge($systemMetaData);

                    $this->dispatcher->project(
                        Serializer::deserialize(json_decode($event->payload, true)),
                        $allMetaData
                    );

                    $this->output->progressAdvance();
                }

                $this->output->progressFinish();
            });

        } catch(Exception $e) {
            $this->error("Projector does not exist!");
            $this->error($e->getMessage());
        }
    }

    /**
     * @param $timeInSeconds
     * @return string
     */
    private function humanReadableExecutionTime($timeInSeconds)
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
