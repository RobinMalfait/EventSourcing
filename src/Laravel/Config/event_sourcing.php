<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Auto Queue
    |--------------------------------------------------------------------------
    |
    | Do you want to queue all the dispatches?
    |
    */
    'autoqueue' => false,

    /*
    |--------------------------------------------------------------------------
    | Pre Rebuild Commands
    |--------------------------------------------------------------------------
    |
    | You can define all the commands you want to execute before you
    | Start the rebuild of your projections from the event store
    |
    | The convention is 'task' => 'title'
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
    */
    'post_rebuild' => [
        'up' => 'Application is going back up'
    ]

];
