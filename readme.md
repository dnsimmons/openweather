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

	composer require dnsimmons/openweather

Add the service provider to your `config/app.php` along with an alias:

    'providers' => [
		...
        Dnsimmons\OpenWeather\OpenWeatherServiceProvider::class,
	];

    'aliases' => [
		...
	    'OpenWeather' => Dnsimmons\OpenWeather\OpenWeather::class,	
	];

## Configure

OpenWeather reads its configuration values from a published app config file.
To publish the required configuration file invoke the following artisan command:

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
	$current = $weather->getCurrentWeatherByCity('Boston');
	print_r($current);

**Output**

	
	Array
	(
	    [weather_updated] => 1550990400
	    [weather_updated_date] => 02-24-2019
	    [weather_updated_time] => 06:40 AM
	    [weather_updated_day] => Sunday
	    [weather_location_name] => Boston
	    [weather_location_latitude] => 42.36
	    [weather_location_longitude] => -71.06
	    [weather_location_country] => US
	    [weather_time_sunrise] => 1551007645
	    [weather_time_sunset] => 1551047281
	    [weather_condition_temp] => 33
	    [weather_condition_name] => Clouds
	    [weather_condition_desc] => overcast clouds
	    [weather_condition_icon] => http://openweathermap.org/img/w/04n.png
	)

### 5 Day 3 Hour Forecast

	$weather  = new OpenWeather();
	$forecast = $weather->getForecastWeatherByCity('Boston');
	print_r($forecast);

**Output**

	Array
	(
	    [weather_location_name] => Boston
	    [weather_location_latitude] => 42.3603
	    [weather_location_longitude] => -71.0583
	    [weather_location_country] => US
	    [weather_forecast] => Array
	        (
	            [0] => Array
	                (
	                    [forecast_updated] => 1550998800
	                    [forecast_updated_date] => 02-24-2019
	                    [forecast_updated_time] => 09:00 AM
	                    [forecast_updated_day] => Sunday
	                    [forecast_condition_temp] => 36
	                    [forecast_condition_name] => Clouds
	                    [forecast_condition_desc] => overcast clouds
	                    [forecast_condition_icon] => http://openweathermap.org/img/w/04n.png
	                )
	
	            [1] => Array
	                (
	                    [forecast_updated] => 1551009600
	                    [forecast_updated_date] => 02-24-2019
	                    [forecast_updated_time] => 12:00 PM
	                    [forecast_updated_day] => Sunday
	                    [forecast_condition_temp] => 37
	                    [forecast_condition_name] => Rain
	                    [forecast_condition_desc] => light rain
	                    [forecast_condition_icon] => http://openweathermap.org/img/w/10d.png
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
