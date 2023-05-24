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
    //Establishing a connection with the MySQL database
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "database"; //database name

    $conn = mysqli_connect($servername, $username, $password, $dbname);
    
    //if incase the connection is not established
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }
       
    //From here on out, It fetches the data from the api and stores the data into the database named as "database"
    //Here the table name is weatherapp
    //and have different columns in the table as location, date, humidity, tempereature, wind, pressure

    // for the search bar functionality
    if (isset($_GET['city'])) {
        // Gets the searched city from the search bar
        $searchedCity = $_GET['city'];

        

//api key used
 //from the openweather api
$apiKey = "f2d3250979dd69e544ddafb8ed2e7225"; 
// API for the current weather conditions using the openweather api
$apiUrl = "https://api.openweathermap.org/data/2.5/weather?q=$searchedCity&dt=&appid=f2d3250979dd69e544ddafb8ed2e7225&units=metric";


$dayData = array();

//initialises a new CURL session and returns the CURL handle
$curl = curl_init($apiUrl);
//Sets the cURL options
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
// Executes the cURL session and sends the HTTP request to the API URL
$apiData = curl_exec($curl);
curl_close($curl); //closes the

//Parses and extracts the relevant data from the API response
$data = json_decode($apiData, true); // Assuming the API response is in JSON format

if ($data && $data['cod'] === 200) {
    $date = date('Y-m-d'); // this Gets the current date
    $location = $searchedCity;
    $temperature = $data['main']['temp'];
    $humidity = $data['main']['humidity'];
    $windSpeed = $data['wind']['speed'];
    $pressure = $data['main']['pressure'];

    // Inserts the data into the database
    $sql = "INSERT INTO weatherapp (date, city, temperature, humidity, wind, pressure) VALUES ('$date', '$location', '$temperature', '$humidity', '$windSpeed', '$pressure')";

    if (mysqli_query($conn, $sql)) {
        echo "Data inserted successfully.";
    } else {
        echo "Error inserting data: " . mysqli_error($conn);
    }
} else {
    echo "Failed to fetch weather data for the city: $searchedCity";
}
    }

    //From here on out, the display property will take place
    
    if (isset($_GET['city'])) {
        // It gets the searched city from the search bar
        $searchedCity = $_GET['city'];
    } else {
        // Sets the default city as mesa
        $searchedCity = "Mesa";
    }
    
    // Retrieves the weather data from the database for the searched city
    $sql = "SELECT * FROM weatherapp WHERE city = '$searchedCity'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {

        // Displays the fetched weather data into the localhost

        echo "<h2>Weather Data for $searchedCity</h2>";
        echo "<table>";
        echo "<tr><th>Date</th><th>Location</th><th>Temperature</th><th>Humidity</th><th>Wind</th><th>Pressure</th></tr>";

        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            echo "<td>" . $row['date'] . "</td>";
            echo "<td>" . $row['city'] . "</td>";
            echo "<td>" . $row['temperature'] . "</td>";
            echo "<td>" . $row['humidity'] . "</td>";
            echo "<td>" . $row['wind'] . "</td>";
            echo "<td>" . $row['pressure'] . "</td>";
            echo "</tr>";
        }

        echo "</table>";
    } else {
        echo "No weather data available for the city: $searchedCity";
    }

// Closes the database connection
mysqli_close($conn);
?>
</body>
</html>
