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

// Step 2: Store the data in the database
$dayData = array();

for ($i = 6; $i >= 0; $i--) {
    $timestamp = strtotime("-$i day");
    $date = date('Y-m-d', $timestamp);
    $day = date('l', $timestamp);

    $sql = "INSERT INTO weatherapp (date, day) VALUES ('$date', '$day')";

    if (mysqli_query($conn, $sql)) {
        $dayData[] = array('date' => $date, 'day' => $day);
       
    } else {
        echo "Error inserting data: " . mysqli_error($conn);
    }
}

// Step 3: Close the database connection
mysqli_close($conn);
?>
