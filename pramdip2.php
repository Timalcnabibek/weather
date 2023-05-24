<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="Pramdip Shrestha_2330784.css">
</head>
<body>
<div class="card1">
    <form action="database.php" method="post">
          </form>
    <h1>Weather Data of last 7 days</h1>
    <hr>
    <br>
    
        <!-- This is an HTML label tag to display a label for the input element. -->
        <label for="city_name">Enter City Name:</label>
        
                <button><svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 1024 1024" height="1.5em"
                width="1.5em" xmlns="http://www.w3.org/2000/svg">
                <path
                  d="M909.6 854.5L649.9 594.8C690.2 542.7 712 479 712 412c0-80.2-31.3-155.4-87.9-212.1-56.6-56.7-132-87.9-212.1-87.9s-155.5 31.3-212.1 87.9C143.2 256.5 112 331.8 112 412c0 80.1 31.3 155.5 87.9 212.1C256.5 680.8 331.8 712 412 712c67 0 130.6-21.8 182.7-62l259.7 259.6a8.2 8.2 0 0 0 11.6 0l43.6-43.5a8.2 8.2 0 0 0 0-11.6zM570.4 570.4C528 612.7 471.8 636 412 636s-116-23.3-158.4-65.6C211.3 528 188 471.8 188 412s23.3-116.1 65.6-158.4C296 211.3 352.2 188 412 188s116.1 23.2 158.4 65.6S636 352.2 636 412s-23.3 116.1-65.6 158.4z">
                </path>
              </svg></button> 
        
    </form>

<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "test";
$conn = mysqli_connect($servername, $username, $password , $dbname);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
// This query creates a table to store weather data.
mysqli_query($conn, "CREATE DATABASE IF NOT EXISTS $dbname");
mysqli_select_db($conn, $dbname);
mysqli_query($conn, "CREATE TABLE IF NOT EXISTS weather (
    cityName  VARCHAR (255) ,
    temperature FLOAT(6,2) ,
    pressure FLOAT(6,2) ,
    humidity FLOAT(6,2) ,
    wind FLOAT(6,2) ,
    `description` VARCHAR(255) ,
    `current_time` VARCHAR(255),
    full_date VARCHAR(255)
)");
// This code block sets the value of $cty_name to user input or a default city name.
if(isset($_POST['city_name'])){
    $cty_name = $_POST['city_name'];
} else {
    $cty_name = 'mesa'; // default city name
}

$start_timestamp = strtotime('-7 days 12:00:00'); // start from 7 days ago at 12 pm
$current_timestamp = strtotime('now');
while ($start_timestamp < $current_timestamp) {
    $start_date = date('Y-m-d', $start_timestamp);
    $datas = json_decode(file_get_contents("https://history.openweathermap.org/data/2.5/history/city?q=".$cty_name.",&type=hour&start=".$start_timestamp."&cnt=24&appid=faf337b47999f8244054ad8ba8ea6159"), true);
    if (!empty($datas)) {
        mysqli_query($conn, "DELETE FROM weather WHERE cityName = '{$cty_name}' AND full_date = '{$start_date}'"); // delete old data for the given date
        foreach($datas['list'] as $data) {
            $unix_timestamp = $data['dt'];
            $date_time = date("Y-m-d H:i:s", $unix_timestamp);
            $current_time = date("H:i:s", $unix_timestamp);
            $full_date = date("Y-m-d", $unix_timestamp);
            if ($current_time === '12:00:00') { // check if the current time is 12 pm
                $temperature_celsius = round($data['main']['temp'] - 273.15, 2); // Convert temperature to Celsius
                $sql = "INSERT INTO weather (temperature, pressure, humidity, wind, `description`, `current_time`, full_date, cityName)
                VALUES ('{$temperature_celsius}', '{$data['main']['pressure']}', '{$data['main']['humidity']}', '{$data['wind']['speed']}', '{$data['weather'][0]['description']}', '{$current_time}', '{$full_date}', '{$cty_name}')";
                mysqli_query($conn, $sql);
            }
        }
    }
    $start_timestamp = strtotime('+1 day', $start_timestamp); // move to the next day
}

$getdata_sql = "SELECT * FROM weather WHERE cityName = '{$cty_name}' ORDER BY full_date DESC"; // fetch only data for the current city
$req_all_data = mysqli_query($conn, $getdata_sql);
if ($req_all_data) {
    echo '<table><thead><tr><th>City Name</th><th>Date</th><th>Temperature</th><th>Pressure</th><th>Humidity</th><th>Wind</th><th>Description</th></tr></thead><tbody>';
   
    // Create an array to hold the data for each day
    $daily_data = array();
   
    // Loop through each row of data
    while ($data = mysqli_fetch_assoc($req_all_data)) {
       
        // Extract the hour from the current_time field
        $hour = date('H', strtotime($data['current_time']));
       
        // If the hour is 12, add this data to the array for the current day
        if ($hour == '12') {
            $full_date = $data['full_date'];
            if (!isset($daily_data[$full_date])) {
                $daily_data[$full_date] = array();
            }
            $daily_data[$full_date][] = $data;
        }
    }
   
    // Loop through the daily data and output it to the table
    foreach ($daily_data as $date => $data_list) {
        // Get the first item in the list to get the cityName
        $cityName = $data_list[0]['cityName'];
       
        // Calculate the average values for each column
        $temp_sum = 0;
        $pressure_sum = 0;
        $humidity_sum = 0;
        $wind_sum = 0;
        $description = '';
        foreach ($data_list as $data) {
            $temp_sum += $data['temperature'];
            $pressure_sum += $data['pressure'];
            $humidity_sum += $data['humidity'];
            $wind_sum += $data['wind'];
            if (empty($description)) {
                $description = $data['description'];
            }
        }
        $count = count($data_list);
        $temperature_avg = round($temp_sum / $count, 2);
        $pressure_avg = round($pressure_sum / $count, 2);
        $humidity_avg = round($humidity_sum / $count, 2);
        $wind_avg = round($wind_sum / $count, 2);
       
// Output the data to the table
echo "<tr><td>{$cityName}</td><td>{$date}</td><td>{$temperature_avg} °C</td><td>{$pressure_avg} hPa</td><td>{$humidity_avg} %</td><td>{$wind_avg} m/s</td><td>{$description}</td></tr>";
    }
    echo '</tbody></table>';
}?>
<script src="pramdip1.js"></script>
</body>
</html>