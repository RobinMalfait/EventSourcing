<?php namespace EventSourcing\Laravel\Commands;

use Illuminate\Console\Command;

class MakeEventStoreTableCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'EventSourcing:table';

    /**
     * The console command description.
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
        var_dump("TEHEEE");
    }
}
