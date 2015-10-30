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

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

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
