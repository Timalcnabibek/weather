<?php
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
            'city' => $cityName, // Updated line
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
        'city' => $daily['city'],
        'temperature' => intval($daily['temperature']),
        'humidity' => intval($daily['humidity']),
        'windSpeed' => intval($daily['windSpeed']),
        'pressure' => intval($daily['pressure']),
    );
}

// Return the weather data as JSON
header('Content-Type: application/json');
echo json_encode($weatherData);

$dataInserted = false; // Flag variable

foreach ($dailyData as $date => $daily) {
    $date = date('Y-m-d', strtotime($date)); // Format the date correctly

    $temperature = intval($daily['temperature']);
    $humidity = intval($daily['humidity']);
    $windSpeed = intval($daily['windSpeed']);
    $pressure = intval($daily['pressure']);

    // Prepare the SQL statement
    $sql = "INSERT INTO weatherapp (date, temperature, humidity, wind, pressure, city) VALUES ('$date', '$temperature', '$humidity', '$windSpeed', '$pressure', '$cityName')";
    // Execute the SQL statement
    if (mysqli_query($conn, $sql)) {
        $dataInserted = true; // Set the flag to true if data is inserted successfully
    } else {
        echo "Error inserting data: " . mysqli_error($conn) . "<br>";
    }
}

if ($dataInserted) {
    echo "Data inserted successfully";
}

mysqli_close($conn);

?>
