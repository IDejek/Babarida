<?php
/**
 * Weather API Integration
 *
 * @package Babarida_Dive_Center
 */

defined('ABSPATH') || exit;

class Babarida_Weather {

    private static $latitude  = 1.4748;
    private static $longitude = 124.8421;

    /**
     * Get current weather data
     */
    public static function get_current() {
        $cached = get_transient('babarida_weather_current');
        if ($cached !== false) return $cached;

        $api_key = get_option('babarida_weather_api_key', '');
        $data    = array();

        if ($api_key) {
            // OpenWeatherMap
            $response = wp_remote_get(add_query_arg(array(
                'lat'   => self::$latitude,
                'lon'   => self::$longitude,
                'appid' => $api_key,
                'units' => 'metric',
            ), 'https://api.openweathermap.org/data/2.5/weather'), array('timeout' => 10));

            if (!is_wp_error($response)) {
                $body = json_decode(wp_remote_retrieve_body($response), true);
                if (isset($body['main'])) {
                    $data = array(
                        'temp'         => round($body['main']['temp']),
                        'feels_like'   => round($body['main']['feels_like']),
                        'humidity'     => $body['main']['humidity'],
                        'wind_speed'   => round($body['wind']['speed']),
                        'wind_deg'     => $body['wind']['deg'] ?? 0,
                        'description'  => ucfirst($body['weather'][0]['description'] ?? ''),
                        'icon_code'    => $body['weather'][0]['icon'] ?? '02d',
                        'visibility'   => ($body['visibility'] ?? 10000) / 1000,
                    );
                }
            }
        }

        // Fallback defaults
        if (empty($data)) {
            $hour = (int) current_time('G');
            $data = array(
                'temp'        => 30,
                'feels_like'  => 33,
                'humidity'    => 78,
                'wind_speed'  => 12,
                'wind_deg'    => 90,
                'description' => $hour < 10 ? 'Sunny' : ($hour < 15 ? 'Partly Cloudy' : 'Cloudy'),
                'icon_code'   => $hour < 10 ? '01d' : '02d',
                'visibility'  => 25,
            );
        }

        // Add diving-specific estimates
        $data['water_temp'] = self::estimate_water_temp();
        $data['dive_visibility'] = self::estimate_dive_visibility($data);
        $data['dive_conditions'] = self::assess_dive_conditions($data);
        $data['icon_emoji'] = self::icon_code_to_emoji($data['icon_code']);

        set_transient('babarida_weather_current', $data, 1800); // 30 min cache
        return $data;
    }

    /**
     * Get forecast (3-day)
     */
    public static function get_forecast() {
        $cached = get_transient('babarida_weather_forecast');
        if ($cached !== false) return $cached;

        $api_key = get_option('babarida_weather_api_key', '');
        $days    = array();

        if ($api_key) {
            $response = wp_remote_get(add_query_arg(array(
                'lat'   => self::$latitude,
                'lon'   => self::$longitude,
                'appid' => $api_key,
                'units' => 'metric',
                'cnt'   => 24, // 3 days * 8 intervals
            ), 'https://api.openweathermap.org/data/2.5/forecast'), array('timeout' => 10));

            if (!is_wp_error($response)) {
                $body = json_decode(wp_remote_retrieve_body($response), true);
                if (isset($body['list'])) {
                    $daily = array();
                    foreach ($body['list'] as $item) {
                        $day = substr($item['dt_txt'], 0, 10);
                        if (!isset($daily[$day])) {
                            $daily[$day] = array('temps' => array(), 'descriptions' => array());
                        }
                        $daily[$day]['temps'][] = $item['main']['temp'];
                        $daily[$day]['descriptions'][] = $item['weather'][0]['description'];
                    }
                    foreach ($daily as $date => $d) {
                        $days[] = array(
                            'date'         => $date,
                            'temp_high'    => round(max($d['temps'])),
                            'temp_low'     => round(min($d['temps'])),
                            'description'  => ucfirst($d['descriptions'][count($d['descriptions']) / 2] ?? ''),
                        );
                    }
                }
            }
        }

        if (empty($days)) {
            for ($i = 0; $i < 3; $i++) {
                $date = date('Y-m-d', strtotime('+' . $i . ' days'));
                $days[] = array(
                    'date'        => $date,
                    'temp_high'   => 31 - $i,
                    'temp_low'    => 25 - $i,
                    'description' => $i === 0 ? 'Partly Cloudy' : 'Sunny',
                );
            }
        }

        set_transient('babarida_weather_forecast', $days, 3600);
        return $days;
    }

    /**
     * Estimate water temperature based on month
     */
    private static function estimate_water_temp() {
        $month = (int) date('n');
        // North Sulawesi water temps range 27-30°C
        $temps = array(28, 28, 28, 29, 29, 29, 29, 29, 29, 29, 28, 28);
        return $temps[$month - 1] ?? 29;
    }

    /**
     * Estimate dive visibility
     */
    private static function estimate_dive_visibility($weather) {
        $base = 25; // meters
        // Reduce for rain/clouds
        if (strpos($weather['description'], 'rain') !== false) $base -= 10;
        if (strpos($weather['description'], 'cloud') !== false) $base -= 5;
        // Reduce for wind
        if ($weather['wind_speed'] > 20) $base -= 8;
        if ($weather['wind_speed'] > 30) $base -= 12;
        return max(5, $base);
    }

    /**
     * Assess dive conditions
     */
    private static function assess_dive_conditions($weather) {
        $score = 0;
        if ($weather['wind_speed'] < 15) $score += 3;
        elseif ($weather['wind_speed'] < 25) $score += 2;
        else $score += 1;

        if ($weather['visibility'] >= 20) $score += 3;
        elseif ($weather['visibility'] >= 10) $score += 2;
        else $score += 1;

        if (strpos($weather['description'], 'rain') === false) $score += 3;
        elseif (strpos($weather['description'], 'light rain') !== false) $score += 2;
        else $score += 1;

        if ($score >= 8) return 'excellent';
        if ($score >= 6) return 'good';
        if ($score >= 4) return 'fair';
        return 'poor';
    }

    /**
     * Map icon code to emoji
     */
    private static function icon_code_to_emoji($code) {
        $map = array(
            '01d' => '☀️', '01n' => '🌙',
            '02d' => '⛅', '02n' => '☁️',
            '03d' => '☁️', '03n' => '☁️',
            '04d' => '☁️', '04n' => '☁️',
            '09d' => '🌧️', '09n' => '🌧️',
            '10d' => '🌦️', '10n' => '🌧️',
            '11d' => '⛈️', '11n' => '⛈️',
            '13d' => '❄️', '13n' => '❄️',
            '50d' => '🌫️', '50n' => '🌫️',
        );
        return $map[$code] ?? '⛅';
    }
}
