# OpenWeather


[![Latest Stable Version](https://poser.pugx.org/dnsimmons/openweather/v/stable)](https://packagist.org/packages/dnsimmons/openweather)
[![Latest Unstable Version](https://poser.pugx.org/dnsimmons/openweather/v/unstable)](https://packagist.org/packages/dnsimmons/openweather)
[![Total Downloads](https://poser.pugx.org/dnsimmons/openweather/downloads)](https://packagist.org/packages/dnsimmons/openweather)
[![License](https://poser.pugx.org/dnsimmons/openweather/license)](https://packagist.org/packages/dnsimmons/openweather)


## About

OpenWeather is a [Laravel](https://laravel.com) package simplifying working with the free [Open Weather Map](https://openweathermap.org) APIs. 

### Supported APIs

The package supports the following free Open Weather Map APIs:

- [Current Weather](https://openweathermap.org/current)
- [4 Day 3 Hour Forecast](https://openweathermap.org/api/hourly-forecast)
- [Onecall Forecast](https://openweathermap.org/api/one-call-api)
- 5 Day Historical (coming soon)

### Free API Limitations

- 60 calls/minute up to 1,000,000 calls/month
- 1000 calls/day when using Onecall requests

### Requirements

- A valid Open Weather Maps API Key (AppID).

## Install

Use [composer](http://getcomposer.org) to install the package

	$ composer require dnsimmons/openweather

Add the service provider to your `config/app.php` along with an alias:

    'providers' => [
		...
        Dnsimmons\OpenWeather\OpenWeatherServiceProvider::class,
	];

    'aliases' => [
		...
	    'OpenWeather' => Dnsimmons\OpenWeather\OpenWeather::class,	
	];


Publish the required package configuration file using the artisan command:

	$ php artisan vendor:publish

Edit the `config/openweather.php` file in your Laravel instance and modify the `api_key` value with your Open Weather Map api key (App ID).

	return [
        'api_key'               => 'your-api-key',
        'api_endpoint_current'  => 'https://api.openweathermap.org/data/2.5/weather?',
        'api_endpoint_forecast' => 'https://api.openweathermap.org/data/2.5/forecast?',
        'api_endpoint_onecall'  => 'https://api.openweathermap.org/data/2.5/onecall?',
        'api_endpoint_icons'    => 'https://openweathermap.org/img/w/',
        'api_lang'              => 'en',
        'format_date'           => 'm/d/Y',
        'format_time'           => 'h:i A',
        'format_day'            => 'l'
	];

## Usage

In the example below we fetch the current weather by postal code.

	$weather = new OpenWeather();
	$current = $weather->getCurrentWeatherByPostal('02111');
	print_r($current);


## Methods

Units can be imperial (default), metric, or kelvin. All methods return an array on success and FALSE on failure.

**getCurrentWeatherByCityName**(*string $city*, *string $units*)

**getCurrentWeatherByCityId**(*int $id*, *string $units*)

**getCurrentWeatherByCoords**(*string $latitude*, *string $longitude*, *string $units*)

**getCurrentWeatherByPostal**(*string $postal*, *string $units*)

**getForecastWeatherByCityName**(*string $city*, *string $units*)

**getForecastWeatherByCityId**(*int $id*, *string $units*)

**getForecastWeatherByCoords**(*string $latitude*, *string $longitude*, *string $units*)

**getForecastWeatherByPostal**(*string $postal*, *string $units*)

**getOnecallWeatherByCoords**(*string $latitude*, *string $longitude*, *string $units*, *string $exclude*)
