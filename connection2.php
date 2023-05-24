<!DOCTYPE html>
<html>
<head>
  <title>Result</title>
  <style>
    table {
      border-collapse: collapse;
    }
    th, td {
      border: 1px solid black;
      padding: 8px;
    }
  </style>
</head>
<body>
  <h2>Day and Date Data</h2>
  <table>
    <tr>
      <th>Day</th>
      <th>Date</th>
      <th>Temperature</th>
      <th>Humidity</th>
      <th>wind-pressure</th>
    </tr>
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

    // Fetch the data from the database
    $sql = "SELECT day,date,temperature,humidity,wind FROM weatherapp";
    $result = $conn->query($sql);

    if ($result === false) {
      echo "Error: " . $sql . "<br>" . $conn->error;
    } else {
      // Check if there are any records
      if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
          $day = $row['day'];
          $date = $row['date'];
          echo '<tr>';
          echo '<td>' . $day . '</td>';
          echo '<td>' . $date . '</td>';
          echo '</tr>';
        }
      } else {
        echo '<tr><td colspan="2">No records found.</td></tr>';
      }
    }
    

    // Close the database connection
    $conn->close();
    ?>
  </table>
</body>
</html>
