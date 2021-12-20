<?php

return [
    'api_key' 				=> env('OPENWEATHER_API_KEY'),
    'api_endpoint_current'  => 'https://api.openweathermap.org/data/2.5/weather?',
    'api_endpoint_forecast' => 'https://api.openweathermap.org/data/2.5/forecast?',
    'api_endpoint_onecall'  => 'https://api.openweathermap.org/data/2.5/onecall?',
    'api_endpoint_history'  => 'https://api.openweathermap.org/data/2.5/onecall/timemachine?',
    'api_endpoint_icons'    => 'https://openweathermap.org/img/w/',
    'api_endpoint_icons_ext' => 'png',
    'api_lang' 				=> env('OPENWEATHER_API_LANG', 'en'),
    'format_date'           => env('OPENWEATHER_API_DATE_FORMAT', 'm/d/Y'),
    'format_time'           => env('OPENWEATHER_API_TIME_FORMAT', 'h:i A'),
    'format_day'            => env('OPENWEATHER_API_DAY_FORMAT', 'l')
];
