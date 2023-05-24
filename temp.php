<?php
// Step 1: Establish a connection to the MySQL database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "database";

$conn = mysqli_connect($servername, $username, $password, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Step 2: Handle the search functionality
if (isset($_GET['city'])) {
    // Get the searched city from the form
    $searchedCity = $_GET['city'];

    // Step 3: Retrieve weather history data from the API for the searched city
 
    $apiUrl = "https://history.openweathermap.org/data/2.5/history/city?q=$searchedCity&appid=a0093535dbaa81ff19b179717620c636";

    // Get the date of 7 days ago
    $dateSevenDaysAgo = date('Y-m-d', strtotime('-7 days'));
    $apiUrl .= $dateSevenDaysAgo;

    $curl = curl_init($apiUrl);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $apiData = curl_exec($curl);
    curl_close($curl);

    // Step 4: Parse and extract relevant data from the API response
    $data = json_decode($apiData, true); // Assuming the API response is in JSON format

    // Step 5: Insert the data into the database
    if (isset($data['forecast']['forecastday'][0]['hour'])) {
        $hourlyData = $data['forecast']['forecastday'][0]['hour'];
        foreach ($hourlyData as $hour) {
            $date = $hour['time'];
            $location = $searchedCity;
            $temperature = $hour['temp_c'];
            $humidity = $hour['humidity'];
            $windSpeed = $hour['wind_speed'];
            $pressure = $hour['pressure_mb'];

            $sql = "INSERT INTO weatherapp (date, location, temperature, humidity, wind, pressure) VALUES ('$date', '$location', '$temperature', '$humidity', '$windSpeed', '$pressure')";

            if (mysqli_query($conn, $sql)) {
                echo "Data inserted successfully.";
            } else {
                echo "Error inserting data: " . mysqli_error($conn);
            }
        }
    } else {
        echo "No weather data available for the past 7 days.";
    }
}

// Step 6: Close the database connection
mysqli_close($conn);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Weather History</title>
</head>
<body>
    <h1>Search for Past Weather Conditions</h1>

    <form action="" method="GET">
        <label for="city">Enter City:</label>
        <input type="text" id="city" name="city" required>
        <button type="submit">Search</button>
    </form>
</body>
</html>
