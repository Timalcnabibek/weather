<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weather Report</title>
</head>
<body>
    <form method="GET">
        <label for="cityName">Enter city name:</label>
        <input type="text" id="cityName" name="cityName">
        <button type="submit">Search</button>
    </form>

    <?php

    if (isset($_GET['cityName'])) {
        $apiKey = '5ffb449047c3a28fc1274437369fb9d3';
        $cityName = $_GET['cityName']; // get the city name from the form input
        $startDate = time() - (7 * 24 * 60 * 60); // start date 7 days ago
        $endDate = time(); // end date (now)

        $url = "http://history.openweathermap.org/data/2.5/history/city?q=$cityName&id=2885679&type=day&appid=5ffb449047c3a28fc1274437369fb9d3";
        
        if (!isset($weather['list'])) {
            echo "Failed to fetch weather data: no data available for the specified city.";
            exit;
        }
        
        // Extract the daily weather data from the API response
        $dailyData = array();
        $hourlyCount = array();
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
            $dailyData[$date]['temperature'] += $hourlyData['main']['temp'] - 273.15;
            $dailyData[$date]['humidity'] += $hourlyData['main']['humidity'];
            $dailyData[$date]['windSpeed'] += $hourlyData['wind']['speed'];
            $dailyData[$date]['pressure'] += $hourlyData['main']['pressure'];
        
            // Keep track of the hourly count for the current date
            $hourlyCount[$date] = isset($hourlyCount[$date]) ? $hourlyCount[$date] + 1 : 1;
        }
        

        // Calculate the daily averages
        // Calculate the daily averages
foreach ($dailyData as &$daily) {
    $date = array_search($daily, $dailyData, true);
    $hourlyCountForDate = $hourlyCount[$date] ?? 1;
    $daily['temperature'] /= $hourlyCountForDate;
    $daily['humidity'] /= $hourlyCountForDate;
    $daily['windSpeed'] /= $hourlyCountForDate;
    $daily['pressure'] /= $hourlyCountForDate;
}

// Print the daily weather data
echo "<h2>Weather Report for {$weather['city']['name']}</h2>";
foreach ($dailyData as $date => $daily) {
    echo "<h3>{$date}</h3>";
    echo "Temperature: " . intval($daily['temperature']) . " ℃<br>";
    echo "Humidity: " . intval($daily['humidity']) . " %<br>";
    echo "Wind Speed: " . intval($daily['windSpeed']) . " m/s<br>";
    echo "Pressure: " . intval($daily['pressure']) . " hPa<br>";
    echo "<hr>";
}
    }
            ?>