<?php

namespace Dnsimmons\OpenWeather;

use Illuminate\Support\Facades\Config;

/**
 * OpenWeather is a Laravel package simplifying working with the free Open Weather Map APIs.
 * https://openweathermap.org/api
 *
 * @package  OpenWeather
 * @author    David Simmons <hello@dsimmons.me>
 * @license    https://opensource.org/licenses/LGPL-3.0 LGPL-3.0
 * @version    1.0.1
 * @since    2019-01-01
 */
class OpenWeather
{

    private $api_key = NULL;
    private $api_endpoint_current = NULL;
    private $api_endpoint_forecast = NULL;
    private $api_endpoint_onecall = NULL;
    private $api_endpoint_icons = NULL;
    private $api_lang = NULL;
    private $format_date = NULL;
    private $format_time = NULL;
    private $format_units = NULL;

    /**
     * Constructor.
     *
     * @return  void
     */
    public function __construct()
    {
        $this->api_key = Config::get('openweather.api_key');
        $this->api_endpoint_current = Config::get('openweather.api_endpoint_current');
        $this->api_endpoint_forecast = Config::get('openweather.api_endpoint_forecast');
        $this->api_endpoint_onecall = Config::get('openweather.api_endpoint_onecall');
        $this->api_endpoint_icons = Config::get('openweather.api_endpoint_icons');
        $this->api_lang = Config::get('openweather.api_lang');
        $this->format_date = Config::get('openweather.format_date');
        $this->format_time = Config::get('openweather.format_time');
        $this->format_day = Config::get('openweather.format_day');
    }

    /**
     * Performs an API request and returns the response.
     * Returns FALSE on failure.
     *
     * @param string $url Request URI
     * @return string|bool
     */
    private function doRequest(string $url)
    {
        $response = @file_get_contents($url);
        return (!$response) ? FALSE : $response;
    }

    /**
     * Parses and returns an OpenWeather current weather API response as an array of formatted values.
     * Returns FALSE on failure.
     *
     * @param string $response OpenWeather API response JSON.
     * @return array|bool
     */
    private function parseCurrentResponse(string $response)
    {

        $struct = json_decode($response, TRUE);
        if (!isset($struct['cod']) || $struct['cod'] != 200) {
            return FALSE;
        }
        return [
            'formats' => [
                'lang' => $this->api_lang,
                'date' => $this->format_date,
                'day' => $this->format_day,
                'time' => $this->format_time,
                'units' => $this->format_units,
            ],
            'datetime' => [
                'timestamp' => $struct['dt'],
                'timestamp_sunrise' => $struct['sys']['sunrise'],
                'timestamp_sunset' => $struct['sys']['sunset'],
                'formatted_date' => date($this->format_date, $struct['dt']),
                'formatted_day' => date($this->format_day, $struct['dt']),
                'formatted_time' => date($this->format_time, $struct['dt']),
                'formatted_sunrise' => date($this->format_time, $struct['sys']['sunrise']),
                'formatted_sunset' => date($this->format_time, $struct['sys']['sunset']),
            ],
            'location' => [
                'id' => (isset($struct['id'])) ? $struct['id'] : 0,
                'name' => $struct['name'],
                'country' => $struct['sys']['country'],
                'latitude' => $struct['coord']['lat'],
                'longitude' => $struct['coord']['lon'],
            ],
            'condition' => [
                'name' => $struct['weather'][0]['main'],
                'desc' => $struct['weather'][0]['description'],
                'icon' => $this->api_endpoint_icons . $struct['weather'][0]['icon'] . '.png',
            ],
            'forecast' => [
                'temp' => round($struct['main']['temp']),
                'temp_min' => round($struct['main']['temp_min']),
                'temp_max' => round($struct['main']['temp_max']),
                'pressure' => round($struct['main']['pressure']),
                'humidity' => round($struct['main']['humidity']),
            ]
        ];
    }

    /**
     * Parses and returns an OpenWeather forecast weather API response as an array of formatted values.
     * Returns FALSE on failure.
     *
     * @param string $response OpenWeather API response JSON.
     * @return array|bool
     */
    private function parseForecastResponse(string $response)
    {
        $struct = json_decode($response, TRUE);
        if (!isset($struct['cod']) || $struct['cod'] != 200) {
            return FALSE;
        }

        $forecast = [];
        foreach ($struct['list'] as $item) {
            $forecast[] = [
                'datetime' => [
                    'timestamp' => $item['dt'],
                    'timestamp_sunrise' => $struct['city']['sunrise'],
                    'timestamp_sunset' => $struct['city']['sunset'],
                    'formatted_date' => date($this->format_date, $item['dt']),
                    'formatted_day' => date($this->format_day, $item['dt']),
                    'formatted_time' => date($this->format_time, $item['dt']),
                    'formatted_sunrise' => date($this->format_time, $struct['city']['sunrise']),
                    'formatted_sunset' => date($this->format_time, $struct['city']['sunset']),
                ],
                'condition' => [
                    'name' => $item['weather'][0]['main'],
                    'desc' => $item['weather'][0]['description'],
                    'icon' => $this->api_endpoint_icons . $item['weather'][0]['icon'] . '.png',
                ],
                'forecast' => [
                    'temp' => round($item['main']['temp']),
                    'temp_min' => round($item['main']['temp_min']),
                    'temp_max' => round($item['main']['temp_max']),
                    'pressure' => round($item['main']['pressure']),
                    'humidity' => round($item['main']['humidity']),
                ]
            ];
        }
        return [
            'formats' => [
                'lang' => $this->api_lang,
                'date' => $this->format_date,
                'day' => $this->format_day,
                'time' => $this->format_time,
                'units' => $this->format_units,
            ],
            'location' => [
                'id' => (isset($struct['city']['id'])) ? $struct['city']['id'] : 0,
                'name' => $struct['city']['name'],
                'country' => $struct['city']['country'],
                'latitude' => $struct['city']['coord']['lat'],
                'longitude' => $struct['city']['coord']['lon'],
            ],
            'forecast' => $forecast
        ];
    }

    /**
     * Parses and returns an OpenWeather onecall weather API response as an array of formatted values.
     * Returns FALSE on failure.
     *
     * @param string $response OpenWeather API response JSON.
     * @return array|bool
     */
    private function parseOnecallResponse(string $response)
    {

        $struct = json_decode($response, TRUE);
        if (!isset($struct['cod']) || $struct['cod'] != 200) {
            // @TODO rght now there is no cod element to check in the API response
        }

        $forecast = [];

        $current = [];
        if (isset($struct['current'])) {
            $current['datetime'] = [
                'timestamp' => $struct['current']['dt'],
                'timestamp_sunrise' => $struct['current']['sunrise'],
                'timestamp_sunset' => $struct['current']['sunset'],
                'formatted_date' => date($this->format_date, $struct['current']['dt']),
                'formatted_day' => date($this->format_day, $struct['current']['dt']),
                'formatted_time' => date($this->format_time, $struct['current']['dt']),
                'formatted_sunrise' => date($this->format_time, $struct['current']['sunrise']),
                'formatted_sunset' => date($this->format_time, $struct['current']['sunset']),
            ];
            $current['condition'] = [
                'name' => $struct['current']['weather'][0]['main'],
                'desc' => $struct['current']['weather'][0]['description'],
                'icon' => $this->api_endpoint_icons . $struct['current']['weather'][0]['icon'] . '.png',
            ];
            $current['forecast'] = [
                'temp' => round($struct['current']['temp']),
                'pressure' => round($struct['current']['pressure']),
                'humidity' => round($struct['current']['humidity']),
            ];
        }

        $minutely = [];
        if (isset($struct['minutely'])) {
            //@TODO implement once better supported by the API
        }

        $hourly = [];
        if (isset($struct['hourly'])) {
            foreach ($struct['hourly'] as $item) {
                $hourly[] = [
                    'datetime' => [
                        'timestamp' => $item['dt'],
                        'timestamp_sunrise' => $struct['current']['sunrise'],
                        'timestamp_sunset' => $struct['current']['sunset'],
                        'formatted_date' => date($this->format_date, $item['dt']),
                        'formatted_day' => date($this->format_day, $item['dt']),
                        'formatted_time' => date($this->format_time, $item['dt']),
                        'formatted_sunrise' => date($this->format_time, $struct['current']['sunrise']),
                        'formatted_sunset' => date($this->format_time, $struct['current']['sunset']),
                    ],
                    'condition' => [
                        'name' => $item['weather'][0]['main'],
                        'desc' => $item['weather'][0]['description'],
                        'icon' => $this->api_endpoint_icons . $item['weather'][0]['icon'] . '.png',
                    ],
                    'forecast' => [
                        'temp' => round($item['temp']),
                        'pressure' => round($item['pressure']),
                        'humidity' => round($item['humidity']),
                    ]
                ];
            }
            $forecast['hourly'] = $hourly;
        }

        $daily = [];
        if (isset($struct['daily'])) {
            foreach ($struct['daily'] as $item) {
                $daily[] = [
                    'datetime' => [
                        'timestamp' => $item['dt'],
                        'timestamp_sunrise' => $item['sunrise'],
                        'timestamp_sunset' => $item['sunset'],
                        'formatted_date' => date($this->format_date, $item['dt']),
                        'formatted_day' => date($this->format_day, $item['dt']),
                        'formatted_time' => date($this->format_time, $item['dt']),
                        'formatted_sunrise' => date($this->format_time, $item['sunrise']),
                        'formatted_sunset' => date($this->format_time, $item['sunset']),
                    ],
                    'condition' => [
                        'name' => $item['weather'][0]['main'],
                        'desc' => $item['weather'][0]['description'],
                        'icon' => $this->api_endpoint_icons . $item['weather'][0]['icon'] . '.png',
                    ],
                    'forecast' => [
                        'temp' => round($item['temp']['day']),
                        'temp_min' => round($item['temp']['min']),
                        'temp_max' => round($item['temp']['max']),
                        'pressure' => round($item['pressure']),
                        'humidity' => round($item['humidity']),
                    ]
                ];
            }
            $forecast['daily'] = $daily;
        }

        return [
            'formats' => [
                'lang' => $this->api_lang,
                'date' => $this->format_date,
                'day' => $this->format_day,
                'time' => $this->format_time,
                'units' => $this->format_units,
            ],
            'location' => [
                'latitude' => $struct['lat'],
                'longitude' => $struct['lon'],
            ],
            'current' => $current,
            'minutely' => $minutely,
            'hourly' => $hourly,
            'daily' => $daily
        ];
    }

    /**
     * Returns an OpenWeather API response for current weather.
     * Returns FALSE on failure.
     *
     * @param array $params Array of request parameters.
     * @return array|bool
     */
    private function getCurrentWeather(array $params)
    {
        $params = http_build_query($params);
        $request = $this->api_endpoint_current . $params;
        $response = $this->doRequest($request);
        if (!$response) {
            return FALSE;
        }
        $response = $this->parseCurrentResponse($response);
        if (!$response) {
            return FALSE;
        }
        return $response;
    }

    /**
     * Returns an OpenWeather API response for forecast weather.
     * Returns FALSE on failure.
     *
     * @param array $params Array of request parameters.
     * @return array|bool
     */
    private function getForecastWeather(array $params)
    {
        $params = http_build_query($params);
        $request = $this->api_endpoint_forecast . $params;
        $response = $this->doRequest($request);
        if (!$response) {
            return FALSE;
        }
        $response = $this->parseForecastResponse($response);
        if (!$response) {
            return FALSE;
        }
        return $response;
    }

    /**
     * Returns an OpenWeather API response for onecall weather.
     * Returns FALSE on failure.
     *
     * @param array $params Array of request parameters.
     * @return array|bool
     */
    private function getOnecallWeather(array $params)
    {
        $params = http_build_query($params);
        $request = $this->api_endpoint_onecall . $params;
        $response = $this->doRequest($request);
        if (!$response) {
            return FALSE;
        }
        $response = $this->parseOnecallResponse($response);
        if (!$response) {
            return FALSE;
        }
        return $response;
    }

    /**
     * Returns current weather by city name.
     * Returns FALSE on failure.
     *
     * @param string $city City name (Boston) or city name and country code (Boston,US).
     * @param string $units Units of measurement (imperial, metric, kelvin)
     * @return array|bool
     */
    public function getCurrentWeatherByCityName(string $city, string $units = 'imperial')
    {
        $this->format_units = $units;
        return $this->getCurrentWeather([
            'q' => $city,
            'units' => $units,
            'lang' => $this->api_lang,
            'appid' => $this->api_key
        ]);
    }

    /**
     * Returns current weather by city ID.
     * Returns FALSE on failure.
     *
     * @param int $id OpenWeather city ID
     * @param string $units Units of measurement (imperial, metric, kelvin)
     * @return array|bool
     */
    public function getCurrentWeatherByCityId(int $id, string $units = 'imperial')
    {
        $this->format_units = $units;
        return $this->getCurrentWeather([
            'id' => $id,
            'units' => $units,
            'lang' => $this->api_lang,
            'appid' => $this->api_key
        ]);
    }

    /**
     * Returns current weather by latitude and longitude.
     * Returns FALSE on failure.
     *
     * @param string $latitude Latitude
     * @param string $longitude Longitude
     * @param string $units Units of measurement (imperial, metric, kelvin)
     * @return array|bool
     */
    public function getCurrentWeatherByCoords(string $latitude, string $longitude, string $units = 'imperial')
    {
        $this->format_units = $units;
        return $this->getCurrentWeather([
            'lat' => $latitude,
            'lon' => $longitude,
            'units' => $units,
            'lang' => $this->api_lang,
            'appid' => $this->api_key
        ]);
    }

    /**
     * Returns current weather by postal code.
     * Returns FALSE on failure.
     *
     * @param string $postal Postal code
     * @param string $units Units of measurement (imperial, metric, kelvin)
     * @return array|bool
     */
    public function getCurrentWeatherByPostal(string $postal, string $units = 'imperial')
    {
        $this->format_units = $units;
        return $this->getCurrentWeather([
            'zip' => $postal,
            'units' => $units,
            'lang' => $this->api_lang,
            'appid' => $this->api_key
        ]);
    }

    /**
     * Returns forecast weather by city name.
     * Returns FALSE on failure.
     *
     * @param string $city City name (Boston) or city name and country code (Boston,US).
     * @param string $units Units of measurement (imperial, metric, kelvin)
     * @return array|bool
     */
    public function getForecastWeatherByCityName(string $city, string $units = 'imperial')
    {
        $this->format_units = $units;
        return $this->getForecastWeather([
            'q' => $city,
            'units' => $units,
            'lang' => $this->api_lang,
            'appid' => $this->api_key
        ]);
    }

    /**
     * Returns forecast weather by city ID.
     * Returns FALSE on failure.
     *
     * @param int $id OpenWeather city ID
     * @param string $units Units of measurement (imperial, metric, kelvin)
     * @return array|bool
     */
    public function getForecastWeatherByCityId(int $id, string $units = 'imperial')
    {
        $this->format_units = $units;
        return $this->getForecastWeather([
            'id' => $id,
            'units' => $units,
            'lang' => $this->api_lang,
            'appid' => $this->api_key
        ]);
    }

    /**
     * Returns forecast weather by latitude and longitude.
     * Returns FALSE on failure.
     *
     * @param string $latitude Latitude
     * @param string $longitude Longitude
     * @param string $units Units of measurement (imperial, metric, kelvin)
     * @return array|bool
     */
    public function getForecastWeatherByCoords(string $latitude, string $longitude, string $units = 'imperial')
    {
        $this->format_units = $units;
        return $this->getForecastWeather([
            'lat' => $latitude,
            'lon' => $longitude,
            'units' => $units,
            'lang' => $this->api_lang,
            'appid' => $this->api_key
        ]);
    }

    /**
     * Returns forecast weather by postal code.
     * Returns FALSE on failure.
     *
     * @param string $postal Postal code
     * @param string $units Units of measurement (imperial, metric, kelvin)
     * @return array|bool
     */
    public function getForecastWeatherByPostal(string $postal, string $units = 'imperial')
    {
        $this->format_units = $units;
        return $this->getForecastWeather([
            'zip' => $postal,
            'units' => $units,
            'lang' => $this->api_lang,
            'appid' => $this->api_key
        ]);
    }

    /**
     * Returns onecast weather by latitude and longitude.
     * Returns FALSE on failure.
     *
     * @param string $latitude Latitude
     * @param string $longitude Longitude
     * @param string $units Units of measurement (imperial, metric, kelvin)
     * @param string $exclude Optional exclude specific data by tag (hourly, daily)
     * @return array|bool
     */
    public function getOnecallWeatherByCoords(string $latitude, string $longitude, string $units = 'imperial', string $exclude = '')
    {
        $this->format_units = $units;
        return $this->getOnecallWeather([
            'lat' => $latitude,
            'lon' => $longitude,
            'part' => 'minutely' . ($exclude != '') ? ',' . $exclude : '',
            'units' => $units,
            'lang' => $this->api_lang,
            'appid' => $this->api_key
        ]);
    }

} // end class
