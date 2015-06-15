<?php namespace EventSourcing\Laravel\Commands;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;

class MakeEventStoreTableCommand extends EventSourcingCommand
{

    private $database;

    function __construct(Builder $database)
    {
        $this->database = $database;
    }


    /**
     *
     */
    public function setup()
    {
        $this->setCommandName('table');
        $this->setCommandDescription('Make the EventStore table.');
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->database->create('eventstore', function(Blueprint $table)
        {
            $table->string('uuid', 50)->index();

            $table->integer('playhead')->unsigned();
            $table->text('metadata');
            $table->text('payload');
            $table->dateTime('recorded_on');

            $table->unique(['uuid', 'playhead']);
        });
    }

}