<?php namespace EventSourcing\Laravel\Commands;

use Illuminate\Database\Schema\Blueprint;

class MakeEventStoreTableCommand extends EventSourcingCommand
{
    protected $signature = "event-sourcing:table";

    protected $description = 'Make the EventStore table.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //        $this->database->create('eventstore', function(Blueprint $table)
//        {
//            $table->string('uuid', 50)->index();
//
//            $table->integer('playhead')->unsigned();
//            $table->text('metadata');
//            $table->text('payload');
//            $table->dateTime('recorded_on');
//
//            $table->unique(['uuid', 'playhead']);
//        });
    }
}
