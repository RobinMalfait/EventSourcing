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
    private $app;

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
        $this->app['db']->connection('eventstore')->getSchemaBuilder()->create('eventstore', function(Blueprint $table)
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
