# Nova Google Polygon Field

[![Latest Version](https://img.shields.io/github/release/yieldstudio/nova-google-polygon?style=flat-square)](https://github.com/yieldstudio/nova-google-polygon/releases)
[![Total Downloads](https://img.shields.io/packagist/dt/yieldstudio/nova-google-polygon?style=flat-square)](https://packagist.org/packages/yieldstudio/nova-google-polygon)

This package allows you to add a Google Map polygon editor on your Laravel Nova resources.

![screenshot](https://raw.githubusercontent.com/YieldStudio/nova-google-polygon/main/screenshot.gif)

## Requirements

- PHP **8.1+**
- Laravel Nova **4.0+**
- Laravel Framework **8.0+**

## Installation

You can install the package in to a Laravel app that uses Nova via composer:

```bash
composer require yieldstudio/nova-google-polygon
```

Publish config file (optional):

```shell
php artisan vendor:publish --provider="YieldStudio\NovaGooglePolygon\FieldServiceProvider"
```

Create an app and enable Places API and create credentials to get your API key
[https://console.developers.google.com](https://console.developers.google.com)

Add the below to your `.env` file

```shell
NOVA_GOOGLE_POLYGON_API_KEY=############################
NOVA_GOOGLE_POLYGON_CENTER_LAT=48.858361
NOVA_GOOGLE_POLYGON_CENTER_LNG=2.336164
```

## Usage

Add the use declaration to your resource and use the fields:

```php
use YieldStudio\NovaGooglePolygon\GooglePolygon;
// ....

GooglePolygon::make('Delivery area'),
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

### Security

If you've found a bug regarding security please mail [contact@yieldstudio.fr](mailto:contact@yieldstudio.fr) instead of using the issue tracker.

## Credits

- [James Hemery](https://github.com/jameshemery) - Developer

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
