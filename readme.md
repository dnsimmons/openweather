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
- [5 Day 3 Hour Forecast](https://openweathermap.org/forecast5)

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
		'api_key' 				=> 'your-api-key',
		'api_endpoint_current'  => 'http://api.openweathermap.org/data/2.5/weather?',
		'api_endpoint_forecast' => 'http://api.openweathermap.org/data/2.5/forecast?',
		'api_lang' 				=> 'en',
	];

## Usage

### Current Weather

	$weather = new OpenWeather();
	$current = $weather->getCurrentWeatherByPostal('02111');
	print_r($current);

**Output**
	
	Array
	(
	    [timestamp] => 1551160643
	    [location] => Array
	        (
	            [name] => Boston
	            [country] => US
	            [latitude] => 42.36
	            [longitude] => -71.06
	        )
	
	    [condition] => Array
	        (
	            [name] => Clear
	            [desc] => clear sky
	            [icon] => http://openweathermap.org/img/w/01n.png
	        )
	
	    [forecast] => Array
	        (
	            [temp] => 26
	            [temp_min] => 24
	            [temp_max] => 28
	            [pressure] => 1016
	            [humidity] => 42
	            [sunrise] => 1551180262
	            [sunset] => 1551220226
	        )
	
	)

### 5 Day 3 Hour Forecast

	$weather  = new OpenWeather();
	$forecast = $weather->getForecastWeatherByPostal('02111');
	print_r($forecast);

**Output**

	Array
	(
	    [location] => Array
	        (
	            [name] => Boston
	            [country] => US
	            [latitude] => 42.3603
	            [longitude] => -71.0583
	        )
	
	    [forecast] => Array
	        (
	            [0] => Array
	                (
	                    [timestamp] => 1551160800
	                    [condition] => Array
	                        (
	                            [name] => Clear
	                            [desc] => clear sky
	                            [icon] => http://openweathermap.org/img/w/01n.png
	                        )
	
	                    [forecast] => Array
	                        (
	                            [temp] => 25
	                            [temp_min] => 25
	                            [temp_max] => 29
	                            [pressure] => 1014
	                            [humidity] => 100
	                        )
	
	                )
	
	            [1] => Array
	                (
	                    [timestamp] => 1551171600
	                    [condition] => Array
	                        (
	                            [name] => Clear
	                            [desc] => clear sky
	                            [icon] => http://openweathermap.org/img/w/01n.png
	                        )
	
	                    [forecast] => Array
	                        (
	                            [temp] => 24
	                            [temp_min] => 24
	                            [temp_max] => 27
	                            [pressure] => 1017
	                            [humidity] => 100
	                        )
	
	                )
		...

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
