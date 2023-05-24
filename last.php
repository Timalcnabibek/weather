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
        $apiKey = "YOUR_API_KEY";
        $dayData = array();

        for ($i = 6; $i >= 0; $i--) {
            $timestamp = strtotime("-$i day");
            $date = date('Y-m-d', $timestamp);
            $day = date('l', $timestamp);

            $apiUrl = "https://api.openweathermap.org/data/2.5/weather?q=$searchedCity&dt=$timestamp&appid=$apiKey&units=metric";

            $curl = curl_init($apiUrl);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            $apiData = curl_exec($curl);
            curl_close($curl);

            $data = json_decode($apiData, true); // Assuming the API response is in JSON format

            if ($data && $data['cod'] === 200) {
                $temperature = $data['main']['temp'];
                $humidity = $data['main']['humidity'];
                $windSpeed = $data['wind']['speed'];
                $pressure = $data['main']['pressure'];

                $sql = "INSERT INTO weatherapp (date, location, temperature, humidity, wind, pressure) VALUES ('$date', '$searchedCity', '$temperature', '$humidity', '$windSpeed', '$pressure')";

                if (mysqli_query($conn, $sql)) {
                    $dayData[] = array('date' => $date, 'day' => $day);
                } else {
                    echo "Error inserting data: " . mysqli_error($conn);
                }
            } else {
                echo "Failed to fetch weather data for the city: $searchedCity";
            }
        }

        echo "<h2>Weather Data for $searchedCity</h2>";
        echo "<table>";
        echo "<tr><th>Date</th><th>Location</th><th>Temperature</th><th>Humidity</th><th>Wind</th><th>Pressure</th></tr>";

            foreach ($dayData as $day) {
        $date = $day['date'];
        $day = $day['day'];

        $sql = "SELECT * FROM weatherapp WHERE date = '$date' AND location = '$searchedCity'";
        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $temperature = $row['temperature'];
            $humidity = $row['humidity'];
            $wind = $row['wind'];
            $pressure = $row['pressure'];

            echo "<tr>";
            echo "<td>$date</td>";
            echo "<td>$searchedCity</td>";
            echo "<td>$temperature</td>";
            echo "<td>$humidity</td>";
            echo "<td>$wind</td>";
            echo "<td>$pressure</td>";
            echo "</tr>";
        } else {
            echo "<tr>";
            echo "<td>$date</td>";
            echo "<td>$searchedCity</td>";
            echo "<td>N/A</td>";
            echo "<td>N/A</td>";
            echo "<td>N/A</td>";
            echo "<td>N/A</td>";
            echo "</tr>";
        }
    }

    echo "</table>";
} else {
    echo "No weather data available for the city: $searchedCity";
}

// Close the database connection
mysqli_close($conn);
?>
</body>
</html>

