<?php

namespace Dnsimmons\OpenWeather;

use Config;

/**
* OpenWeather is a Laravel package simplifying working with the free Open Weather Map APIs.
* https://openweathermap.org/api
*
* @package  OpenWeather
* @author 	David Simmons <hello@dsimmons.me>
* @license 	https://opensource.org/licenses/LGPL-3.0 LGPL-3.0
* @version 	1.0.1 Updated & fixed by R. Vale <valenew@gmail.com> on 2020-06-28
* @since    2019-01-01
*/
class OpenWeather{

	private $api_key               = NULL;
	private $api_endpoint_current  = NULL;
	private $api_endpoint_forecast = NULL;
	private $api_endpoint_onecall  = NULL;
	private $api_lang 	           = NULL;

	/**
	 * Constructor.
	 *
	 * @return  void 	 
	 */
	public function __construct(){
		$this->api_key 				 = Config::get('openweather.api_key');
		$this->api_endpoint_current  = Config::get('openweather.api_endpoint_current');
		$this->api_endpoint_forecast = Config::get('openweather.api_endpoint_forecast');
		$this->api_endpoint_onecall  = Config::get('openweather.api_endpoint_onecall');
		$this->api_lang 			 = Config::get('openweather.api_lang');
	}

	/**
	 * Performs an API request and returns the response.
	 * Returns FALSE on failure.
	 * 
	 * @param  string $url Request URI
	 * @return string
	 */
	private function doRequest(string $url){
		$response = @file_get_contents($url);
		return (!$response) ? FALSE : $response;
	}

	/**
	 * Parses and returns an OpenWeather current weather API response as an array of formatted values.
	 * Returns FALSE on failure.
	 * 
	 * @param  string $response OpenWeather API response JSON.
	 * @return array
	 */
	private function parseCurrentResponse(string $response){
		$struct = json_decode($response, TRUE);
		if(!isset($struct['cod']) || $struct['cod'] != 200){
			return FALSE;
		}
		return [
			'timestamp' => $struct['dt'],
			'location'  => [
				'name'      => $struct['name'], 
				'country'   => $struct['sys']['country'],
				'latitude'  => $struct['coord']['lat'],
				'longitude' => $struct['coord']['lon'],
			],
			'condition' => [
				'name'     	=> $struct['weather'][0]['main'],
				'desc'     	=> $struct['weather'][0]['description'],
				'icon' 	 	=> 'http://openweathermap.org/img/w/'.$struct['weather'][0]['icon'].'.png',
			],
			'forecast'  => [
				'temp' 	    => round($struct['main']['temp']),
				'temp_min'  => round($struct['main']['temp_min']),
				'temp_max'  => round($struct['main']['temp_max']),
				'pressure' 	=> round($struct['main']['pressure']),
				'humidity'	=> round($struct['main']['humidity']),
				'sunrise'   => $struct['sys']['sunrise'],
				'sunset'    => $struct['sys']['sunset'],
			]
		];
	}

	/**
	 * Parses and returns an OpenWeather forecast weather API response as an array of formatted values.
	 * Returns FALSE on failure.
	 * 
	 * @param  string $response OpenWeather API response JSON.
	 * @return array
	 */
	private function parseForecastResponse(string $response){
		$struct   = json_decode($response, TRUE);
		if(!isset($struct['cod']) || $struct['cod'] != 200){
			return FALSE;
		}
		$forecast = [];
		foreach($struct['list'] as $item){
			$forecast[] = [
				'timestamp' => $item['dt'],	
				'condition' => [
					'name'     	=> $item['weather'][0]['main'],
					'desc'     	=> $item['weather'][0]['description'],
					'icon' 	 	=> 'http://openweathermap.org/img/w/'.$item['weather'][0]['icon'].'.png',
				],
				'forecast'  => [
					'temp' 	    => round($item['main']['temp']),
					'temp_min'  => round($item['main']['temp_min']),
					'temp_max'  => round($item['main']['temp_max']),
					'pressure' 	=> round($item['main']['pressure']),
					'humidity'	=> round($item['main']['humidity']),
				]
			];
		}
		return [
			'location'  => [
				'name'      => $struct['city']['name'], 
				'country'   => $struct['city']['country'],
				'latitude'  => $struct['city']['coord']['lat'],
				'longitude' => $struct['city']['coord']['lon'],
			],
			'forecast'  => $forecast
		];
	}

	/**
	 * Returns an OpenWeather API response for current weather.
	 * Returns FALSE on failure.
	 * 
	 * @param  array $params Array of request parameters.
	 * @return array
	 */
	private function getCurrentWeather(array $params){
		$params   = http_build_query($params);
		$request  = $this->api_endpoint_current.$params;
		$response = $this->doRequest($request);
		if(!$response){
			return FALSE;
		}
		$response = $this->parseCurrentResponse($response);
		if(!$response){
			return FALSE;
		}
		return $response;
	}

	/**
	 * Returns an OpenWeather API response for forecast weather.
	 * Returns FALSE on failure.
	 * 
	 * @param  array $params Array of request parameters.
	 * @return array
	 */
	private function getForecastWeather(array $params){
		$params   = http_build_query($params);
		$request  = $this->api_endpoint_forecast.$params;
		$response = $this->doRequest($request);
		if(!$response){
			return FALSE;
		}
		$response = $this->parseForecastResponse($response);
		if(!$response){
			return FALSE;
		}
		return $response;
	}


	/**
	 * Returns current weather by city name.
	 * Returns FALSE on failure.
	 * 
	 * @param  string $city  City name (Boston) or city name and country code (Boston,US).
	 * @param  string $units Units of measurement (imperial, metric, kelvin)
	 * @return array
	 */
	public function getCurrentWeatherByCityName(string $city, string $units='imperial'){
		return $this->getCurrentWeather([
			'q'       => $city,
			'units'   => $units,
			'lang'    => $this->api_lang,
			'appid'   => $this->api_key
		]);
	}

	/**
	 * Returns current weather by city ID.
	 * Returns FALSE on failure.
	 * 
	 * @param  int    $id    OpenWeather city ID
	 * @param  string $units Units of measurement (imperial, metric, kelvin)
	 * @return array
	 */
	public function getCurrentWeatherByCityId(int $id, string $units='imperial'){
		return $this->getCurrentWeather([
			'id'      => $city,
			'units'   => $units,
			'lang'    => $this->api_lang,
			'appid'   => $this->api_key
		]);
	}

	/**
	 * Returns current weather by latitude and longitude.
	 * Returns FALSE on failure.
	 * 
	 * @param  string $latitude  Latitude
	 * @param  string $longitude Longitude
	 * @param  string $units 	 Units of measurement (imperial, metric, kelvin)
	 * @return array
	 */
	public function getCurrentWeatherByCoords(string $latitude, string $longitude, string $units='imperial'){
		return $this->getCurrentWeather([
			'lat'     => $latitude,
			'lon'     => $longitude,
			'units'   => $units,
			'lang'    => $this->api_lang,
			'appid'   => $this->api_key
		]);
	}

	/**
	 * Returns current weather by postal code.
	 * Returns FALSE on failure.
	 * 
	 * @param  string $postal Postal code
	 * @param  string $units  Units of measurement (imperial, metric, kelvin)
	 * @return array
	 */
	public function getCurrentWeatherByPostal(string $postal, string $units='imperial'){
		return $this->getCurrentWeather([
			'zip'     => $postal,
			'units'   => $units,
			'lang'    => $this->api_lang,
			'appid'   => $this->api_key
		]);
	}

	/**
	 * Returns forecast weather by city name.
	 * Returns FALSE on failure.
	 * 
	 * @param  string $city  City name (Boston) or city name and country code (Boston,US).
	 * @param  string $units Units of measurement (imperial, metric, kelvin)
	 * @return array
	 */
	public function getForecastWeatherByCityName(string $city, string $units='imperial'){
		return $this->getForecastWeather([
			'q'       => $city,
			'units'   => $units,
			'lang'    => $this->api_lang,
			'appid'   => $this->api_key
		]);
	}

	/**
	 * Returns forecast weather by city ID.
	 * Returns FALSE on failure.
	 * 
	 * @param  int    $id    OpenWeather city ID
	 * @param  string $units Units of measurement (imperial, metric, kelvin)
	 * @return array
	 */
	public function getForecastWeatherByCityId(int $id, string $units='imperial'){
		return $this->getForecastWeather([
			'id'      => $city,
			'units'   => $units,
			'lang'    => $this->api_lang,
			'appid'   => $this->api_key
		]);
	}

	/**
	 * Returns forecast weather by latitude and longitude.
	 * Returns FALSE on failure.
	 * 
	 * @param  string $latitude  Latitude
	 * @param  string $longitude Longitude
	 * @param  string $units 	 Units of measurement (imperial, metric, kelvin)
	 * @return array
	 */
	public function getForecastWeatherByCoords(string $latitude, string $longitude, string $units='imperial'){
		return $this->getForecastWeather([
			'lat'     => $latitude,
			'lon'     => $longitude,
			'units'   => $units,
			'lang'    => $this->api_lang,
			'appid'   => $this->api_key
		]);
	}

	/**
	 * Returns forecast weather by posta code.
	 * Returns FALSE on failure.
	 * 
	 * @param  string $postal Postal code
	 * @param  string $units  Units of measurement (imperial, metric, kelvin)
	 * @return array
	 */
	public function getForecastWeatherByPostal(string $postal, string $units='imperial'){
		return $this->getForecastWeather([
			'zip'     => $postal,
			'units'   => $units,
			'lang'    => $this->api_lang,
			'appid'   => $this->api_key
		]);
	}
	
	
	///////////////////////////////////////////////////////////////////////////////////////
	
	/**
	 * Parses and returns an OpenWeather forecast weather API response as an array of formatted values.
	 * Returns FALSE on failure.
	 * 
	 * @param  string $response OpenWeather API response JSON.
	 * @return array
	 */
	 function parseOneCallResponse(string $response){
		$struct = json_decode($response, TRUE);

	 	if(!isset($struct['lat']) || !isset($struct['lon'])){
			return FALSE;
		}
		return [
		 	'timestamp' => $struct['current']['dt'],
		 	'timezone' => $struct['timezone'],
			'location'  => [
				'latitude'  => $struct['lat'],
				'longitude' => $struct['lon'],
			],
			'condition' => [
				'name'     	=> $struct['current']['weather'][0]['main'],
				'desc'     	=> $struct['current']['weather'][0]['description'],
				'icon' 	 	=> 'http://openweathermap.org/img/w/'.$struct['current']['weather'][0]['icon'].'.png',
			],
			'forecast'  => [
				'daily'  => $struct['daily']
				
			]
		];
	}
	
	/**
	 * Returns an OpenWeather API response with one call.
	 * Returns FALSE on failure.
	 * 
	 * @param  array $params Array of request parameters.
	 * @return array
	 */
	private function getOneCallWeather(array $params){
		$params   = http_build_query($params);
		$request  = $this->api_endpoint_onecall.$params;

		$response = $this->doRequest($request);
		if(!$response){
			return FALSE;
		}
		$response = $this->parseOneCallResponse($response);
		if(!$response){
			return FALSE;
		}
		return $response;
	}
	
	/**
	 * Returns one call weather by latitude and longitude.
	 * Returns FALSE on failure.
	 * 
	 * @param  string $latitude  Latitude
	 * @param  string $longitude Longitude
	 * @param  string $units 	 Units of measurement (imperial, metric, kelvin)
	 * @param  string exclude	 part (optional parameter) by using this parameter you can exclude some parts of weather data from the API response. 
	 *				 The value parts should be a comma-delimited list (without spaces). 
	 *				 Available values:
	 *					current
	 *					minutely
	 *					hourly
	 *					daily
	 *				 Please note that the minute forecast is only available for certain regions.
	 * @return array
	 */
	public function getOneCallWeatherByCoords(string $latitude, string $longitude, string $units='imperial', string $exclude='hourly,minutely'){
		
		return $this->getOneCallWeather([
			'lat'     => $latitude,
			'lon'     => $longitude,
			'units'   => $units,
			'exclude' => $exclude,
			'lang'    => $this->api_lang,
			'appid'   => $this->api_key
		]);
	}

} // end class
