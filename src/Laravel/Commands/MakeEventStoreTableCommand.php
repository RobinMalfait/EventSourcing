<?php namespace EventSourcing\Laravel\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;

class MakeEventStoreTableCommand extends Command
{
    /**
     * The Schema Builder
     * @var
     */
    private $schema;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "event-sourcing:table";

    /**
     * The console command description
     *
     * @var string
     */
    protected $description = 'Make the EventStore table.';

    function __construct($app)
    {
        $this->schema = $app['db']->connection('eventstore')->getSchemaBuilder();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->schema->create('eventstore', function(Blueprint $table)
        {
            $table->string('uuid', 50);

            $table->integer('playhead')->unsigned();
            $table->text('metadata');
            $table->text('payload');
            $table->dateTime('recorded_on');
            $table->text('type');

            $table->unique(['uuid', 'playhead']);
        });
    }
}
