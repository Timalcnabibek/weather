<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weather Data</title>
</head>
<body>

<form method="get">
    <label for="cityName">Enter City Name:</label>
    <input type="text" id="cityName" name="cityName" placeholder="Mesa">
    <button type="submit">Submit</button>
</form>

<?php
// Establishing a connection with the MySQL database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "database"; // database name

$conn = mysqli_connect($servername, $username, $password, $dbname);

// if in case the connection is not established
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if (isset($_GET['cityName'])) {
    $apiKey = '5ffb449047c3a28fc1274437369fb9d3';
    $cityName = $_GET['cityName']; // get city name from form input
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
        $dailyData[$date]['temperature'] += $hourlyData['main']['temp'] - 273.15;
        $dailyData[$date]['humidity'] += $hourlyData['main']['humidity'];
        $dailyData[$date]['windSpeed'] += $hourlyData['wind']['speed'];
        $dailyData[$date]['pressure'] += $hourlyData['main']['pressure'];

        // Keep track of the hourly count for the current date
        $hourlyCount[$date] = isset($hourlyCount[$date]) ? $
        $hourlyCount[$date] : array();

            // Increment the hourly count for the current hour
    $hour = date('H');
    $hourlyCount[$date][$hour] = isset($hourlyCount[$date][$hour]) ?
        $hourlyCount[$date][$hour] + 1 : 1;

    // Update the hourly count in the database
    $sql = "UPDATE hourly_counts SET count = ? WHERE date = ? AND hour = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array($hourlyCount[$date][$hour], $date, $hour));

    // Update the daily count in the database
    $sql = "UPDATE daily_counts SET count = count + 1 WHERE date = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array($date));
}

// Close the database connection
$pdo = null;
}


?>