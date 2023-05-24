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

// Step 2: Retrieve and display the data from the database
$sql = "SELECT * FROM weatherapp";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    echo "<table>";
    echo "<tr><th>Date</th><th>Day</th></tr>";
    
    while ($row = mysqli_fetch_assoc($result)) {
        $date = $row['date'];
        $day = $row['day'];

        echo "<tr><td>$date</td><td>$day</td></tr>";
    }

    echo "</table>";
} else {
    echo "No data found.";
}

// Step 3: Close the database connection
mysqli_close($conn);
?>
