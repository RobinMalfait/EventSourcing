<?php namespace EventSourcing\Laravel\Commands;

use EventSourcing\Domain\MetaData;
use EventSourcing\Serialization\Serializer;
use Exception;
use Illuminate\Console\Command;
use ReflectionClass;

class Migrate1To2 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "event-sourcing:1to2 {from=eventstore_backup} {to=eventstore}";

    /**
     * The console command description
     *
     * @var string
     */
    protected $description = 'Convert EventStore version 1 to version 2. Use with caution!';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->convertToNewSyntax(
            $this->argument('from'),
            $this->argument('to')
        );
    }

    private function convertToNewSyntax($fromTable, $toTable)
    {
        $events = $this->getEvents($fromTable);

        $serializer = app()->make(Serializer::class);

        \DB::connection('eventstore')->table($toTable)->truncate();

        $this->comment("Converting..." . PHP_EOL);

        $this->output->progressStart(count($events));

        $events->each(function ($data) use ($toTable, $serializer) {
            try {
                // Full Event
                $event = $this->deserialize(json_decode($data->payload, true));

                // Serialized Event
                $eventSerialized = $serializer->serialize($event);
                $metaDataSerialized = $serializer->serialize(MetaData::deserialize($event->getMetaData()));

                // Store
                $this->insertInto($toTable, [
                    'uuid' => $data->uuid,
                    'version' => $data->version,
                    'payload' => json_encode($eventSerialized),
                    'metadata' => json_encode($metaDataSerialized),
                    'type' => $data->type,
                    'recorded_on' => $data->recorded_on
                ]);
            } catch (Exception $e) {
                $this->info(print_r(json_decode($data->payload, true)));
            }

            $this->output->progressAdvance();
        });

        $this->output->progressFinish();

        $this->info("Converted :)");
    }

    private function getEvents($from)
    {
        return collect(\DB::connection('eventstore')->table($from)->get());
    }

    public function deserialize($data)
    {
        $eventClass = array_keys($data)[0];

        return $this->deserializeRecursively($eventClass, $data[$eventClass]);
    }
    private function deserializeRecursively($class, $data)
    {
        $reflectionClass = new ReflectionClass($class);
        $obj = $reflectionClass->newInstanceWithoutConstructor();

        foreach ($reflectionClass->getProperties() as $property) {
            $property->setAccessible(true);
            $propertyName = $property->getName();
            $contents = $data[$propertyName];

            if (isset($contents['type'])) {
                $property->setValue($obj,
                    $this->deserializeRecursively(
                        $contents['type'],
                        $contents['value']
                    )
                );
            } else {
                $property->setValue($obj, $data[$propertyName]);
            }
        }

        return $obj;
    }

    private function insertInto($table, $data)
    {
        \DB::connection('eventstore')->table($table)->insert($data);
    }
}
