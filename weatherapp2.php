


<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Establishing a connection with the MySQL database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "database"; // Replace with your database name

$conn = mysqli_connect($servername, $username, $password, $dbname);
// Fetch weather data
if (isset($_GET['city']) && !empty($_GET['city'])) {
    $searchedCity = $_GET['city'];
} else {
    $searchedCity = "Mesa";
}


// Your API key
$apiKey = 'da98a262e080e996ea17f7ddef9d765e'; // Replace with your OpenWeatherMap API key
$apiUrl = "https://api.openweathermap.org/data/2.5/weather?q=$searchedCity&appid=$apiKey&units=metric";

$curl = curl_init($apiUrl);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
$apiData = curl_exec($curl);
curl_close($curl);

$data = json_decode($apiData, true);

if ($data && $data['cod'] === 200) {
    $date = date('Y-m-d');
    $temperature = $data['main']['temp'];
    $humidity = $data['main']['humidity'];
    $windSpeed = $data['wind']['speed'];
    $pressure = $data['main']['pressure'];

    // Check if the data already exists in the database
    $checkSql = "SELECT * FROM weatherapp WHERE date = '$date' AND temperature = '$temperature' AND humidity = '$humidity' AND wind = '$windSpeed' AND pressure = '$pressure' AND city = '$searchedCity'";
    $checkResult = mysqli_query($conn, $checkSql);

    if (mysqli_num_rows($checkResult) == 0) {
        // Insert the data into the database
        $insertSql = "INSERT INTO weatherapp (date, temperature, humidity, wind, pressure, city) VALUES ('$date', '$temperature', '$humidity', '$windSpeed', '$pressure', '$searchedCity')";

        if (mysqli_query($conn, $insertSql)) {
            $response = array("status" => "success", "message" => "Data inserted successfully");
            echo json_encode($response);
            exit; // Exit the script to prevent further output
        } else {
            $response = array("status" => "error", "message" => "Error inserting data: " . mysqli_error($conn));
            echo json_encode($response);
            exit; // Exit the script to prevent further output
        }
    } else {
        $response = array("status" => "error", "message" => "Data already exists in the database");
        echo json_encode($response);
        exit; // Exit the script to prevent further output
    }
} else {
    $response = array("status" => "error", "message" => "Failed to fetch weather data for the city: $searchedCity");
    echo json_encode($response);

    exit; // Exit the script to prevent further output
}

// The following code will only be reached if there were no errors

// Retrieves the weather data from the database for the searched city
if (isset($_GET['city'])) {
    $searchedCity = $_GET['city'];
} else {
    $searchedCity = "Mesa";
}

$sql = "SELECT date, temperature, humidity, wind, pressure, city FROM weatherapp WHERE city = '$searchedCity'";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    // Prepare the weather data as an associative array
    $weatherData = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $weatherData[] = array(
            'date' => $row['date'],
            'temperature' => $row['temperature'],
            'humidity' => $row['humidity'],
            'windSpeed' => $row['wind'],
            'pressure' => $row['pressure']
        );
    }

    // Output the weather data in JSON format
    header('Content-Type: application/json');
    echo json_encode($weatherData);
} else {
    $response = array("status" => "error", "message" => "No weather data found.");
    echo json_encode($response);
}

// Close the database connection
mysqli_close($conn);
?>


 

