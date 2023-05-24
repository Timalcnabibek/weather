<?php
$apiKey = '5ffb449047c3a28fc1274437369fb9d3';
$cityName = 'Mesa'; // specify the city name
$startDate = time() - (7 * 24 * 60 * 60); // start date 7 days ago
$endDate = time(); // end date (now)

$url = "http://history.openweathermap.org/data/2.5/history/city?q={$cityName}&type=day&start={$startDate}&end={$endDate}&appid={$apiKey}";

$data = file_get_contents($url);
if (!$data) {
    echo "Failed to fetch weather data: " . error_get_last()['message'];
    exit;
}

$weather = json_decode($data, true);
if (!$weather) {
    echo "Failed to parse weather data: " . json_last_error_msg();
    exit;
}

// Extract the daily weather data from the API response
$dailyData = array();
foreach ($weather['list'] as $hourlyData) {
    $date = date('Y-m-d', $hourlyData['dt']);
    if (!isset($dailyData[$date])) {
        $dailyData[$date] = array(
            'location' => $weather['city']['name'] ?? '',
            'temperature' => 0,
            'humidity' => 0,
            'windSpeed' => 0,
            'pressure' => 0,
        );
    }

    // Accumulate the data for each day
    $dailyData[$date]['temperature'] += $hourlyData['main']['temp'];
    $dailyData[$date]['humidity'] += $hourlyData['main']['humidity'];
    $dailyData[$date]['windSpeed'] += $hourlyData['wind']['speed'];
    $dailyData[$date]['pressure'] += $hourlyData['main']['pressure'];
}

// Calculate the daily averages
foreach ($dailyData as &$daily) {
    $count = count($weather['list']); // number of hourly data points
    $daily['temperature'] /= $count;
    $daily['humidity'] /= $count;
    $daily['windSpeed'] /= $count;
    $daily['pressure'] /= $count;
}

// Print the daily weather data
foreach ($dailyData as $date => $daily) {
    echo "Date: {$date}<br>";
    echo "Location: {$daily['location']}<br>";
    echo "Temperature: " . intval($daily['temperature']) . "<br>";
    echo "Humidity: " . intval($daily['humidity']) . "<br>";
    echo "Wind Speed: " . intval($daily['windSpeed']) . "<br>";
    echo "Pressure: " . intval($daily['pressure']) . "<br>";
    echo "<hr>";
}

?>
