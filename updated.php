<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
      body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        table {
            border-collapse: collapse;
            font-size: 1rem;
            width: 80%;
            max-width: 600px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
            font-weight: 700;
            background-color: lightblue;
        }

        th {
            background-color: black;
            font-weight: bold;
            color: white;
        }
        
        input {
            
            padding:6px;
      border-radius: 15px 5px;
        }
        body{
            background-image:url(R.jpg)
        }
       
        

    </style>
</head>
<body>


<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Establishing a connection with the MySQL database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "database"; // Replace with your database name

$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check if the connection is successful
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Set default city name
$cityName = "Mesa";

if (isset($_GET['city'])) {
    // Get the searched city from the search bar
    $cityName = $_GET['city'];
}

// Fetch weather data
$apiKey = '5ffb449047c3a28fc1274437369fb9d3';
$startDate = time() - (6 * 24 * 60 * 60); // Start date 7 days ago
$endDate = time(); // End date (now)

$url = "http://history.openweathermap.org/data/2.5/history/city?q=$cityName&type=day&start={$startDate}&end={$endDate}&appid={$apiKey}";

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
    $hourlyCount[$date] = isset($hourlyCount[$date]) ? $hourlyCount[$date] + 1 : 1;
}

// Calculate the daily averages
foreach ($dailyData as &$daily) {
    $date = array_search($daily, $dailyData, true);
    $hourlyCountForDate = $hourlyCount[$date] ?? 1;
    $daily['temperature'] /= $hourlyCountForDate;
    $daily['humidity'] /= $hourlyCountForDate;
    $daily['windSpeed'] /= $hourlyCountForDate;
    $daily['pressure'] /= $hourlyCountForDate;
}

// Prepare the weather data as JSON
$weatherData = array();
foreach ($dailyData as $date => $daily) {
    $weatherData[] = array(
        'date' => $date,
        'temperature' => intval($daily['temperature']),
        'humidity' => intval($daily['humidity']),
        'windSpeed' => intval($daily['windSpeed']),
        'pressure' => intval($daily['pressure']),
    );
}

// Display the weather data in a table
echo '<table>';
echo '<thead>';
echo '<tr>';
echo '<th>Date</th>';
echo '<th>Temperature </th>';
echo '<th>Humidity</th>';
echo '<th>Wind Speed</th>';
echo '<th>Pressure</th>';
echo '</tr>';
echo '</thead>';
echo '<tbody>';
foreach ($weatherData as $data) {
    echo '<tr>';
    echo '<td>' . $data['date'] . '</td>';
    echo '<td>' . $data['temperature'] . '</td>';
    echo '<td>' . $data['humidity'] . '</td>';
    echo '<td>' . $data['windSpeed'] . '</td>';
    echo '<td>' . $data['pressure'] . '</td>';
    echo '</tr>';
}
echo '</tbody>';
echo '</table>';

mysqli_close($conn);
?>

    
</body>
</html>
