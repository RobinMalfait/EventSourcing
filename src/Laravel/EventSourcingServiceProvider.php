<?php namespace EventSourcing\Laravel;

use EventSourcing\Laravel\Commands\MakeAggregateCommand;
use EventSourcing\Laravel\Commands\MakeAggregateRepositoryCommand;
use EventSourcing\Laravel\Commands\MakeEventStoreTableCommand;
use Illuminate\Support\ServiceProvider;

class EventSourcingServiceProvider extends ServiceProvider
{
    protected $commands = [
        'MakeEventStoreTable',
        'MakeAggregate',
        'MakeAggregateRepository',
    ];
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerArtisanCommands();

        $this->commands(
            'command.event-sourcing.table.create',
            'command.event-sourcing.make.aggregate',
            'command.event-sourcing.make.aggregate-repository'
        );
    }

    private function registerArtisanCommands()
    {
        foreach ($this->commands as $command) {
            $this->{"register{$command}Command"}();
        }
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
        ];
    }
}
