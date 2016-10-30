<?php namespace EventSourcing\Laravel\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Contracts\Config\Repository as Config;
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
     * @var string
     */
    protected $table;

    /**
     * @var string
     */
    protected $connection;

    public function __construct()
    {
        parent::__construct();

        $config = app()->make(Config::class);

        $this->table = $config->get('event_sourcing.table_name', 'eventstore');
        $this->connection = $config->get('event_sourcing.connection_name', 'eventstore');
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
            $schema = app()->make('db')
                ->connection($this->connection)
                ->getSchemaBuilder();

            if (!$schema->hasTable($this->table)) {
                $schema->create($this->table, function (Blueprint $table) {
                    $table->increments('id');

                    $table->string('uuid', 36);
                    $table->integer('version')->unsigned();
                    $table->text('metadata');
                    $table->text('payload');
                    $table->text('type');
                    $table->dateTime('recorded_on');

                    $table->unique(['uuid', 'version']);
                });
            }
            $success = true;
        } catch (Exception $e) {
            $this->error("This error has occurred: " . $e->getMessage());

            $this->info("Make sure that you have an `" . $this->connection . "` database connection." . PHP_EOL . "For example:");

            $data = [
                "'" . $this->connection . "' => [",
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
