# EventSourcing

<!-- [![Latest Version on Packagist](https://img.shields.io/packagist/v/league/EventSourcing.svg?style=flat-square)](https://packagist.org/packages/league/EventSourcing) -->
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
<!-- [![Build Status](https://img.shields.io/travis/thephpleague/EventSourcing/master.svg?style=flat-square)](https://travis-ci.org/thephpleague/EventSourcing) -->
<!-- [![Coverage Status](https://img.shields.io/scrutinizer/coverage/g/thephpleague/EventSourcing.svg?style=flat-square)](https://scrutinizer-ci.com/g/thephpleague/EventSourcing/code-structure) -->
<!-- [![Quality Score](https://img.shields.io/scrutinizer/g/thephpleague/EventSourcing.svg?style=flat-square)](https://scrutinizer-ci.com/g/thephpleague/EventSourcing) -->
<!-- [![Total Downloads](https://img.shields.io/packagist/dt/league/EventSourcing.svg?style=flat-square)](https://packagist.org/packages/league/EventSourcing) -->

This is a simple EventSourcing package that you can use in your projects.
This project is written using PSR2

## Install

Via Composer

``` bash
$ composer require robin-malfait/event-sourcing
```

## Usage

Register the service provider

``` php
'providers' => [
    ...
    \EventSourcing\Laravel\EventSourcingServiceProvider::class,
]
```

Publish the configuration file

``` php
php artisan vendor:publish --provider="EventSourcing\Laravel\EventSourcingServiceProvider"
```

The config file looks like this:

[Config File](src/Laravel/Config/event_sourcing.php)

You can now tweak some configurations

Last but not least make the event store table:

```
php artisan event-sourcing:table
```

## Update v1 to v2

If you are still using the first version you better update to version 2.
You will have less problems in the future, I promise.

In Version 2 we give each DomainEvent the responsibility to give data and receive data.
Those methods are 

> `serialize();` Which returns an array of serialized  data
>
> `deserialize(array $data);` Which has a parameter with the data that basically comes from the serialize method. This method should also return an instance of the current event.

### For Example:
 

```php 
<?php namespace App\Users\Events;

use EventSourcing\Domain\DomainEvent;

class UserWasRegistered implements DomainEvent
{
    private $user_id;

    private $email;

    private $password; // Yes, this is encrypted

    public function __construct($user_id, $email, $password)
    {
        $this->user_id = $user_id;
        $this->email = $email;
        $this->password = $password;
    }

    /**
     * @return UserId
     */
    public function getAggregateId()
    {
        return $this->user_id;
    }

    public function getMetaData() 
    {
        return []; // Could be for example the logged in user, ...
    }

    /**
     * @return array
     */
    public function serialize()
    {
        return [
            'user_id' => $this->user_id,
            'email' => $this->email,
            'password' => $this->password
        ];
    }

    /**
     * @param array $data
     * @return mixed
     */
    public static function deserialize(array $data)
    {
        return new static(
            $data['user_id'],
            $data['email'],
            $data['password']
        );
    }
}

```

Once you have defined every serialize / deserialize method in your events you can start the migration process.

In your database rename `eventstore` to `eventstore_backup`

Now you can run the following command in your terminal:

```
php artisan event-sourcing:table
```

This will create the eventstore, now you should see 2 tables in your database

1. `eventstore_backup` => Your old table with all data in
2. `eventstore` => Your new *empty* table

I also have written a helper method to do the migration now.

```
php artisan event-sourcing:1to2 eventstore_backup eventstore
```

Or you can also just run the following command because *eventstore_backup* and *eventstore* are the defaults.

```bash
php artisan event-sourcing:1to2
```

## Testing

``` bash
$ composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email malfait.robin@gmail.com instead of using the issue tracker.

## Credits

- [Robin Malfait](https://github.com/RobinMalfait)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
