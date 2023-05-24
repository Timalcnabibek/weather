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

// Fetch weather data from the API
$apiKey = 'da98a262e080e996ea17f7ddef9d765e';
$startDate = time() - (6 * 24 * 60 * 60); // Start date 7 days ago
$endDate = time(); // End date (now)

$url = "http://history.openweathermap.org/data/2.5/history/city?q=$cityName&type=day&start={$startDate}&end={$endDate}&appid={$apiKey}";

$data = file_get_contents($url);
if (!$data) {
    echo "Failed to fetch weather data from the API: " . error_get_last()['message'];
    exit;
}

$weatherFromAPI = json_decode($data, true);
if (!$weatherFromAPI) {
    echo "Failed to parse weather data from the API: " . json_last_error_msg();
    exit;
}

// Fetch weather data from the database
$sql = "SELECT * FROM weatherapp WHERE city='$cityName'";
$result = mysqli_query($conn, $sql);

$weatherFromDB = array();
if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $weatherFromDB[] = array(
            'date' => $row['date'],
            'temperature' => intval($row['temperature']),
            'humidity' => intval($row['humidity']),
            'windSpeed' => intval($row['windSpeed']),
            'pressure' => intval($row['pressure']),
        );
    }
} else {
    echo "No weather data found in the database.";
    exit;
}

// Merge the weather data from the API and the database
$mergedWeatherData = array_merge($weatherFromAPI, $weatherFromDB);

// Return the merged weather data as JSON
header('Content-Type: application/json');
echo json_encode($mergedWeatherData);

mysqli_close($conn);
?>
