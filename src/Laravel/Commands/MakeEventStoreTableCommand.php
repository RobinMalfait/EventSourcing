<?php namespace EventSourcing\Laravel\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Database\Schema\Blueprint;

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

    public function __construct($app)
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
        $success = false;
        try {
            $this->app['db']->connection('eventstore')->getSchemaBuilder()->create('eventstore', function (Blueprint $table) {
                $table->string('uuid', 50);

                $table->integer('playhead')->unsigned();
                $table->text('metadata');
                $table->text('payload');
                $table->dateTime('recorded_on');
                $table->text('type');

                $table->unique(['uuid', 'playhead']);
            });
            $success = true;
        } catch (Exception $e) {
            $this->error("This error has occurred: " . $e->getMessage());

            $this->info("Make sure that you have an `evenstore` database connection." . PHP_EOL . "For example:");

            $this->table(['key', 'value'], [
                ['driver', 'mysql'],
                ['host', "env('EVENTSTORE_DB_HOST', 'localhost')"],
                ['database', "env('EVENTSTORE_DB_DATABASE', '')"],
                ['username', "env('EVENTSTORE_DB_USERNAME', '')"],
                ['password', "env('EVENTSTORE_DB_PASSWORD', '')"],
            ]);
        }

        if ($success) {
            $this->info("The EventStore has been created!");
        }
    }
}
