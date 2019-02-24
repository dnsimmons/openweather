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
* @version 	1.0.0
* @since    2019-01-01
*/
class OpenWeather{

	private $api_key               = NULL;
	private $api_endpoint_current  = NULL;
	private $api_endpoint_forecast = NULL;
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
			'weather_updated' 			 => $struct['dt'],
			'weather_updated_date' 	     => date('m-d-Y', $struct['dt']),
			'weather_updated_time' 		 => date('h:i A', $struct['dt']),
			'weather_updated_day' 	     => date('l', $struct['dt']),
			'weather_location_name'      => $struct['name'], 
			'weather_location_latitude'  => $struct['coord']['lat'],
			'weather_location_longitude' => $struct['coord']['lon'],
			'weather_location_country'   => $struct['sys']['country'],
			'weather_time_sunrise'       => $struct['sys']['sunrise'],
			'weather_time_sunset'        => $struct['sys']['sunset'],
			'weather_condition_temp' 	 => round($struct['main']['temp']),
			'weather_condition_name'     => $struct['weather'][0]['main'],
			'weather_condition_desc'     => $struct['weather'][0]['description'],
			'weather_condition_icon' 	 => 'http://openweathermap.org/img/w/'.$struct['weather'][0]['icon'].'.png',
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
				'forecast_updated' 			 => $item['dt'],
				'forecast_updated_date' 	 => date('m-d-Y', $item['dt']),
				'forecast_updated_time' 	 => date('h:i A', $item['dt']),
				'forecast_updated_day' 	     => date('l', $item['dt']),		
				'forecast_condition_temp' 	 => round($item['main']['temp']),
				'forecast_condition_name'    => $item['weather'][0]['main'],
				'forecast_condition_desc'    => $item['weather'][0]['description'],
				'forecast_condition_icon' 	 => 'http://openweathermap.org/img/w/'.$item['weather'][0]['icon'].'.png',
			];
		}
		return [
			'weather_location_name'      => $struct['city']['name'], 
			'weather_location_latitude'  => $struct['city']['coord']['lat'],
			'weather_location_longitude' => $struct['city']['coord']['lon'],
			'weather_location_country'   => $struct['city']['country'],
			'weather_forecast' 			 => $forecast,
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
			'lng'     => $longitude,
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
			'lng'     => $longitude,
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

} // end class