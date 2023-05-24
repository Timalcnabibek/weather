<?php
include('updated2.php');

// Establishing a connection with the MySQL database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "database"; // Replace with your database name

$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check if the connection is successful
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Fetch data from the database
$sql = "SELECT * FROM weatherapp"; 
$result = mysqli_query($conn, $sql);

// Creates an array to store the fetched data
$data = array();

// Check if there are any records
if (mysqli_num_rows($result) > 0) {
    // Loop through each row in the result set
    while ($row = mysqli_fetch_assoc($result)) {
        // Add the row data to the data array
        $data[] = $row;
    }
}

// Close the database connection
mysqli_close($conn);

// Convert the data to JSON
$jsonData = json_encode($data);

// Display the JSON data on the website
header('Content-Type: application/json');
echo $jsonData;
?>
