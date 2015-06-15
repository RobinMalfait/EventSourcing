<?php namespace EventSourcing\Laravel;

use EventSourcing\Laravel\Commands\MakeEventStoreTableCommand;
use Illuminate\Support\ServiceProvider;

class EventSourcingServiceProvider extends ServiceProvider
{

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerArtisanCommands();
    }

    private function registerArtisanCommands()
    {
        $this->commands(
            MakeEventStoreTableCommand::class
        );
    }
}
