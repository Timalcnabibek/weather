<!DOCTYPE html>
<html>
<head>
    <title>Weather History</title>
    <style>
        table {
            border-collapse: collapse;
        }

        th, td {
            padding: 8px;
            border: 1px solid black;
        }
    </style>
</head>
<body>
    <h1>Weather History</h1>
    <?php include ('temp.php')?>
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

    // Step 2: Fetch and display the stored data from the database
    $sql = "SELECT * FROM weatherapp";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        echo "<table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Location</th>
                    <th>Temperature</th>
                    <th>Humidity</th>
                    <th>Wind Speed</th>
                    <th>Pressure</th>
                </tr>
            </thead>
            <tbody>";
    
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>
                    <td>".$row['date']."</td>
                    <td>".$row['location']."</td>
                    <td>".$row['temperature']."</td>
                    <td>".$row['humidity']."</td>
                    <td>".$row['wind']."</td>
                    <td>".$row['pressure']."</td>
                </tr>";
        }
    
        echo "</tbody>
        </table>";
    } else {
        echo "No weather data available.";
    }

    // Step 3: Close the database connection
    mysqli_close($conn);
    ?>
</body>
</html>
