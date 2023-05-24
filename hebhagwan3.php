<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weather Data</title>
    <style>
        table {
            border-collapse: collapse;
            margin: 0 auto;
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
            padding: 6px;
            border-radius: 15px 5px;
        }

        body {
            background-image: url(R.jpg);
        }
    </style>
</head>
<body>
    <form method="get" action="">
        <input type="text" name="city" placeholder="Enter a city" value="Mesa">
        <input type="submit" value="Search">
    </form>
    <br><br>

    <?php
    //Establishing a connection with the MySQL database
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "database"; //database name

    $conn = mysqli_connect($servername, $username, $password, $dbname);

    //if in case the connection is not established
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }
    // Set default city name
    $cityName = "Mesa";

    if (isset($_GET['city'])) {
        // Gets the searched city from the search bar
        $cityName = $_GET['city'];
    }

    // Fetch weather data
    $apiKey = '5ffb449047c3a28fc1274437369fb9d3';
    $startDate = time() - (6 * 24 * 60 * 60); // start date 7 days ago
    $endDate = time(); // end date (now)

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
    $hourlyCount = array(); // Keep track of hourly count for each date

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
            $hourlyCount[$date] = 0; // Initialize hourly count to 0
        }

        // Accumulate the data for each day
        $dailyData[$date]['temperature'] += $hourlyData['main']['temp'] - 273.15;
        $dailyData[$date]['humidity'] += $hourlyData['main']['humidity'];
        $dailyData[$date]['windSpeed'] += $hourlyData['wind']['speed'];
        $dailyData[$date]['pressure'] += $hourlyData['main']['pressure'];

        // Increment the hourly count for the current date
        $hourlyCount[$date]++;
    }

    // Calculate the average values for each day
    foreach ($dailyData as $date => $daily) {
        $temperatureSum = $daily['temperature'];
        $humiditySum = $daily['humidity'];
        $windSpeedSum = $daily['windSpeed'];
        $pressureSum = $daily['pressure'];

        // Calculate the average values only if hourly data is available
        if ($hourlyCount[$date] > 0) {
            $dailyData[$date]['temperature'] = $temperatureSum / $hourlyCount[$date];
            $dailyData[$date]['humidity'] = $humiditySum / $hourlyCount[$date];
            $dailyData[$date]['windSpeed'] = $windSpeedSum / $hourlyCount[$date];
            $dailyData[$date]['pressure'] = $pressureSum / $hourlyCount[$date];
        } else {
            // If no hourly data is available, remove the entry from dailyData
            unset($dailyData[$date]);
        }
    }

    // Prepare the SQL query to insert the data into the table
$insertQuery = "INSERT INTO weatherapp (city, date, temperature, humidity, wind, pressure)
VALUES ('{$daily['location']}', '{$date}', '{$daily['temperature']}', '{$daily['humidity']}',
        '{$daily['windSpeed']}', '{$daily['pressure']}')";

// Execute the SQL query
if (mysqli_query($conn, $insertQuery)) {
echo "Data inserted successfully!";
} else {
echo "Error inserting data: " . mysqli_error($conn);
}

    // Print the daily weather data in a table
    echo "<table>";
    echo "<h2 style='color: white;'>Weather Data for <span style='color: yellow;'>$cityName</span></h2>";
    echo "<tr><th>Date</th><th>Temperature</th><th>Humidity</th><th>Wind Speed</th><th>Pressure</th></tr>";

    foreach ($dailyData as $date => $daily) {
        echo "<tr>";
        echo "<td>{$date}</td>";
        echo "<td>" . round($daily['temperature'], 2) . "</td>";
        echo "<td>" . round($daily['humidity'], 2) . "</td>";
        echo "<td>" . round($daily['windSpeed'], 2) . "</td>";
        echo "<td>" . round($daily['pressure'], 2) . "</td>";
        echo "</tr>";
    }

    echo "</table>";
    ?>
</body>
</html>




