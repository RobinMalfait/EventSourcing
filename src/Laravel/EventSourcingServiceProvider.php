<?php namespace EventSourcing\Laravel;

use EventSourcing\EventDispatcher\EventSourcingEventDispatcher;
use EventSourcing\EventDispatcher\EventDispatcher;
use EventSourcing\EventSourcing\EventSourcingRepository;
use EventSourcing\EventStore\EventStore;
use EventSourcing\EventStore\EventStoreRepository;
use EventSourcing\Laravel\Commands\MakeAggregateCommand;
use EventSourcing\Laravel\Commands\MakeAggregateCommandCommand;
use EventSourcing\Laravel\Commands\MakeAggregateRepositoryCommand;
use EventSourcing\Laravel\Commands\MakeEventStoreTableCommand;
use EventSourcing\Laravel\Commands\RebuildProjectionsCommand;
use EventSourcing\Laravel\Commands\ScaffoldAggregateCommand;
use EventSourcing\Laravel\EventStore\MysqlEventStore;
use Illuminate\Support\ServiceProvider;

class EventSourcingServiceProvider extends ServiceProvider
{
    protected $commands = [
        'MakeEventStoreTable',
        'MakeAggregate',
        'MakeAggregateRepository',
        'MakeAggregateCommand',
        'ScaffoldAggregate',
        'MakeServiceProvider',
        'RebuildProjections'
    ];
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(EventDispatcher::class, EventSourcingEventDispatcher::class);
        $this->app->singleton(EventStore::class, MysqlEventStore::class);

        $this->app->singleton(EventStoreRepository::class, EventSourcingRepository::class);

        $this->registerArtisanCommands();
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

    public function registerMakeAggregateCommandCommand()
    {
        $this->app->singleton('command.event-sourcing.make.aggregate-command', function () {
            return new MakeAggregateCommandCommand();
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

    public function registerMakeServiceProviderCommand()
    {
        $this->app->singleton('command.event-sourcing.make.service-provider', function () {
            return new MakeServiceProviderCommand();
        });
    }


    public function registerRebuildProjectionsCommand()
    {
        $this->app->singleton('command.event-sourcing.rebuild-projections', function () {
            return new RebuildProjectionsCommand($this->app);
        });
    }


    public function provides()
    {
        return [
            'command.event-sourcing.table.create',
            'command.event-sourcing.make.aggregate',
            'command.event-sourcing.make.aggregate-repository',
            'command.event-sourcing.make.aggregate-command',
            'command.event-sourcing.make.scaffold',
            'command.event-sourcing.make.service-provider',
            'command.event-sourcing.rebuild-projections',
        ];
    }
}
