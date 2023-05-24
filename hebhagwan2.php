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
    // Set default city name
    $cityName = "Mesa";

    if (isset($_GET['city'])) {
        // Gets the searched city from the search bar
        $cityName = $_GET['city'];
    }

    // Database configuration
    $host = 'localhost';
    $dbname = 'database';
    $username = 'root';
    $password = '';

    try {
        // Create a new PDO instance
        $db = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);

        // Set error mode to exception
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Prepare and execute the query
        $query = "SELECT * FROM weatherapp WHERE city = :cityName";
        $statement = $db->prepare($query);
        $statement->bindParam(':cityName', $cityName);
        $statement->execute();

        // Fetch the weather data
        $weatherData = $statement->fetchAll(PDO::FETCH_ASSOC);

        // Print the table header
        echo "<table>";
        echo "<h2 style='color: white;'>Weather Data for <span style='color: yellow;'>$cityName</span></h2>";
        echo "<tr><th>Date</th><th>Temperature</th><th>Humidity</th><th>Wind Speed</th><th>Pressure</th></tr>";

        // Print the weather data
        if ($weatherData) {
            foreach ($weatherData as $data) {
                echo "<tr>";
                echo "<td>{$data['date']}</td>";
                echo "<td>{$data['temperature']}</td>";
                echo "<td>{$data['humidity']}</td>";
                echo "<td>{$data['wind']}</td>";
                echo "<td>{$data['pressure']}</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='5'>No weather data found for $cityName</td></tr>";
        }

        echo "</table>";
    } catch (PDOException $e) {
        echo "Failed to connect to the database: " . $e->getMessage();
    }
    ?>
</body>
</html>
