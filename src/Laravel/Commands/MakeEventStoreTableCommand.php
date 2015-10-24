<?php namespace EventSourcing\Laravel\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Database\Schema\Blueprint;

class MakeEventStoreTableCommand extends Command
{
    /**
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
                $table->increments('id');
                $table->string('uuid', 50);

                $table->integer('version')->unsigned();
                $table->text('payload');
                $table->text('type');
                $table->dateTime('recorded_on');

                $table->unique(['uuid', 'version']);
            });
            $success = true;
        } catch (Exception $e) {
            $this->error("This error has occurred: " . $e->getMessage());

            $this->info("Make sure that you have an `eventstore` database connection." . PHP_EOL . "For example:");

            $data = [
                "'eventstore' => [",
                "\t'driver'    => 'mysql',",
                "\t'host'      => env('EVENTSTORE_DB_HOST', 'localhost'),",
                "\t'database'  => env('EVENTSTORE_DB_DATABASE', 'forge'),",
                "\t'username'  => env('EVENTSTORE_DB_USERNAME', 'forge'),",
                "\t'password'  => env('EVENTSTORE_DB_PASSWORD', ''),",
                "\t'charset'   => 'utf8',",
                "\t'collation' => 'utf8_unicode_ci',",
                "\t'prefix'    => '',",
                "\t'strict'    => false,",
                "],"
            ];

            $this->info(implode(PHP_EOL, $data));
        }

        if ($success) {
            $this->info("The EventStore has been created!");
        }
    }
}
