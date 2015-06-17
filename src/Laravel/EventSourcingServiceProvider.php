<?php namespace EventSourcing\Laravel;

use EventSourcing\EventSourcing\EventSourcingRepository;
use EventSourcing\EventStore\EventStore;
use EventSourcing\EventStore\EventStoreRepository;
use EventSourcing\Laravel\Commands\MakeAggregateCommand;
use EventSourcing\Laravel\Commands\MakeAggregateRepositoryCommand;
use EventSourcing\Laravel\Commands\MakeEventStoreTableCommand;
use EventSourcing\Laravel\Commands\ScaffoldAggregateCommand;
use EventSourcing\Laravel\EventStore\MysqlEventStore;
use Illuminate\Support\ServiceProvider;

class EventSourcingServiceProvider extends ServiceProvider
{
    protected $commands = [
        'MakeEventStoreTable',
        'MakeAggregate',
        'MakeAggregateRepository',
        'ScaffoldAggregate',
    ];
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerArtisanCommands();

        $this->app->singleton(EventStore::class, MysqlEventStore::class);
        $this->app->singleton(EventStoreRepository::class, EventSourcingRepository::class);
    }

    private function registerArtisanCommands()
    {
        foreach ($this->commands as $command) {
            $this->{"register{$command}Command"}();
        }

        $this->commands($this->provides());
    }

    public function registerMakeAggregateCommand()
    {
        $this->app->singleton('command.event-sourcing.make.aggregate', function () {
            return new MakeAggregateCommand();
        });
    }

    public function registerMakeAggregateRepositoryCommand()
    {
        $this->app->singleton('command.event-sourcing.make.aggregate-repository', function () {
            return new MakeAggregateRepositoryCommand();
        });
    }

    public function registerScaffoldAggregateCommand()
    {
        $this->app->singleton('command.event-sourcing.make.scaffold', function () {
            return new ScaffoldAggregateCommand($this->app);
        });
    }

    public function registerMakeEventStoreTableCommand()
    {
        $this->app->singleton('command.event-sourcing.table.create', function () {
            return new MakeEventStoreTableCommand($this->app);
        });
    }

    public function provides()
    {
        return [
            'command.event-sourcing.table.create',
            'command.event-sourcing.make.aggregate',
            'command.event-sourcing.make.aggregate-repository',
            'command.event-sourcing.make.scaffold',
        ];
    }
}
