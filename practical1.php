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

        // Step 3: Retrieve weather history data from the database for the searched city
        $sql = "SELECT * FROM weatherapp WHERE location = '$searchedCity'";
        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) > 0) {
            // Step 4: Display the fetched weather data
            echo "<h2>Weather Data for $searchedCity</h2>";
            echo "<table>";
            echo "<tr><th>Date</th><th>Location</th><th>Temperature</th><th>Humidity</th><th>Wind</th><th>Pressure</th></tr>";

            while ($row = mysqli_fetch_assoc($result)) {
                $date = $row['date'];
                $location = $row['location'];
                $temperature = $row['temperature'];
                $humidity = $row['humidity'];
                $wind = $row['wind'];
                $pressure = $row['pressure'];

                echo "<tr>";
                echo "<td>$date</td>";
                echo "<td>$location</td>";
                echo "<td>$temperature</td>";
                echo "<td>$humidity</td>";
                echo "<td>$wind</td>";
                echo "<td>$pressure</td>";
                echo "</tr>";
            }

            echo "</table>";
        } else {
            echo "No weather data available for the city: $searchedCity";
        }
    }

    // Close the database connection
    mysqli_close($conn);
    ?>
</body>
</html>
