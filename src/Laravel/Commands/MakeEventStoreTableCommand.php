<?php namespace EventSourcing\Laravel\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Schema\Blueprint;

class MakeEventStoreTableCommand extends Command
{
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

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->database->connection('eventstore')->create('eventstore', function(Blueprint $table)
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
