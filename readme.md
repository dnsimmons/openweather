# OpenWeather


[![Latest Stable Version](https://poser.pugx.org/dnsimmons/openweather/v/stable)](https://packagist.org/packages/dnsimmons/openweather)
[![Latest Unstable Version](https://poser.pugx.org/dnsimmons/openweather/v/unstable)](https://packagist.org/packages/dnsimmons/openweather)
[![Total Downloads](https://poser.pugx.org/dnsimmons/openweather/downloads)](https://packagist.org/packages/dnsimmons/openweather)
[![License](https://poser.pugx.org/dnsimmons/openweather/license)](https://packagist.org/packages/dnsimmons/openweather)


## About

OpenWeather is a [Laravel](https://laravel.com) package simplifying working with the free [Open Weather Map](https://openweathermap.org) APIs. 

OpenWeather takes care of making requests to various free Open Weather Map APIs and
returns well-structured easy to use weather data including conditions, temperatures, humidity, 
pressure, location, and timestamp data.

Take a look at the Example Usage section output below for a typical structured response.

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

## Example Usage

In the example below we fetch the current weather by postal code.

	$weather = new OpenWeather();
	$current = $weather->getCurrentWeatherByPostal('02111');
	print_r($current);

**Output**

    Array
    (
        [formats] => Array
            (
                [lang] => en
                [date] => m/d/Y
                [day] => l
                [time] => h:i A
                [units] => imperial
            )
    
        [datetime] => Array
            (
                [timestamp] => 1593387767
                [timestamp_sunrise] => 1593335394
                [timestamp_sunset] => 1593390304
                [formatted_date] => 06/28/2020
                [formatted_day] => Sunday
                [formatted_time] => 11:42 PM
                [formatted_sunrise] => 09:09 AM
                [formatted_sunset] => 12:25 AM
            )
    
        [location] => Array
            (
                [id] => 4930956
                [name] => Boston
                [country] => US
                [latitude] => 42.36
                [longitude] => -71.06
            )
    
        [condition] => Array
            (
                [name] => Rain
                [desc] => light rain
                [icon] => https://openweathermap.org/img/w/10d.png
            )
    
        [forecast] => Array
            (
                [temp] => 67
                [temp_min] => 64
                [temp_max] => 68
                [pressure] => 1007
                [humidity] => 88
            )
    
    )

## Methods

All methods return an array on success and FALSE on failure.

### Current Weather Methods

**getCurrentWeatherByCityName**(*string $city*, *string $units*)

**Params**
- City Name (example: Boston)
- Units (imperial (default), metric, or kelvin)

**getCurrentWeatherByCityId**(*int $id*, *string $units*)

**Params**
- City ID
- Units (imperial (default), metric, or kelvin)

**getCurrentWeatherByCoords**(*string $latitude*, *string $longitude*, *string $units*)

**Params**
- Latitude
- Longitude
- Units (imperial (default), metric, or kelvin)

**getCurrentWeatherByPostal**(*string $postal*, *string $units*)

**Params**
- US Postal Code
- Units (imperial (default), metric, or kelvin)

### 4 Day 3 Hour Forecast Methods

**getForecastWeatherByCityName**(*string $city*, *string $units*)

**Params**
- City Name (example: Boston)
- Units (imperial (default), metric, or kelvin)

**getForecastWeatherByCityId**(*int $id*, *string $units*)

**Params**
- City ID
- Units (imperial (default), metric, or kelvin)

**getForecastWeatherByCoords**(*string $latitude*, *string $longitude*, *string $units*)

**Params**
- Latitude
- Longitude
- Units (imperial (default), metric, or kelvin)

**getForecastWeatherByPostal**(*string $postal*, *string $units*)

**Params**
- US Postal Code
- Units (imperial (default), metric, or kelvin)

### Onecall Request Methods

**getOnecallWeatherByCoords**(*string $latitude*, *string $longitude*, *string $units*, *string $exclude*)

**Params**
- Latitude
- Longitude
- Units (imperial (default), metric, or kelvin)
- Exclude (optional comma separated values: current,hourly,daily) 
