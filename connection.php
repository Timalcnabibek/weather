<?php
// Replace with your MySQL credentials
$dbHost = 'localhost';
$dbUser = 'root';
$dbPass = '';
$dbName = 'database';

// Create a new connection
$conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

for ($i = 6; $i >= 0; $i--) {
    $date = strtotime("-$i days");
    $day = date("l", $date);
    $formattedDate = date("Y-m-d", $date);

    // Insert the data into the database
    $sql = "INSERT INTO weatherapp (day, date) VALUES ('$day', '$formattedDate')";
    $result = $conn->query($sql);

    if ($result === false) {
        echo "Error: " . $sql . "<br>" . $conn->error;
    } 

}

// Close the database connection
$conn->close();
?>


