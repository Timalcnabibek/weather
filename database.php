<?php
// Step 1: Establish a connection to the MySQL database
$servername = "localhost";
$username = "weatherapp";
$password = "";
$dbname = "database";

$conn = mysqli_connect($servername, $username, $password, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Step 2: Retrieve data from the database
$sql = "SELECT * FROM weather_history";
$result = mysqli_query($conn, $sql);

// Step 3: Display the data in a table format
if (mysqli_num_rows($result) > 0) {
    echo "<table>
            <tr>
                <th>Date</th>
                <th>Location</th>
                <th>Temperature</th>
                <th>Humidity</th>
            </tr>";
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>
                <td>".$row['date']."</td>
                <td>".$row['location']."</td>
                <td>".$row['temperature']."</td>
                <td>".$row['humidity']."</td>
            </tr>";
    }
    echo "</table>";
} else {
    echo "No data available.";
}

// Step 4: Close the database connection
mysqli_close($conn);
?>
