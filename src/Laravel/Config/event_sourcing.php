<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Auto Queue
    |--------------------------------------------------------------------------
    |
    | Do you want to queue all the listeners?
    |
    | When you set this to false, and you want a certain listener to queue
    | You should implement the Illuminate\Contracts\Queue\ShouldQueue interface
    |
    */
    'autoqueue' => false,

    /*
    |--------------------------------------------------------------------------
    | Table Name
    |--------------------------------------------------------------------------
    |
    | The name of the eventstore table
    |
    */
    'table_name' => 'eventstore',

    /*
    |--------------------------------------------------------------------------
    | Connection name
    |--------------------------------------------------------------------------
    |
    | The name of the eventstore connection
    |
    */
    'connection_name' => 'eventstore',

    /*
    |--------------------------------------------------------------------------
    | Disable Queues On Projections Rebuild
    |--------------------------------------------------------------------------
    |
    | You can put your projections in a queue job, but
    | When you rebuild your projections, this is slow
    | With this option you can disable the queue
    |
    */
    'disable_projection_queue' => true,

    /*
    |--------------------------------------------------------------------------
    | Pre Rebuild Commands
    |--------------------------------------------------------------------------
    |
    | You can define all the commands you want to execute before you
    | Start the rebuild of your projections from the event store
    |
    | The convention is 'task' => 'title'
    | If you have options you can write the 'task' like
    |
    | E.x.: 'mail example@example.com --queued=true'
    */
    'pre_rebuild' => [
        'down' => 'Application is going down',
        'migrate:reset' => 'Reset all migrations',
        'migrate' => 'Migrate all migrations'
    ],

    /*
    |--------------------------------------------------------------------------
    | Post Rebuild Commands
    |--------------------------------------------------------------------------
    |
    | You can define all the commands you want to execute after you
    | did the rebuild of your projections from the event store
    |
    | The convention is 'task' => 'title'
    | If you have options you can write the 'task' like
    |
    | E.x.: 'mail example@example.com --queued=true'
    */
    'post_rebuild' => [
        'up' => 'Application is going back up'
    ]

];
