
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weather Data of Last 7 Days</title>

    <style>
        table {
            border-collapse: collapse;
            margin: 0 auto;
            font-size: 1rem;
            width: 80%;
            max-width: 600px;
            background-color: rgba(255, 255, 255, 0.8); /* Set a semi-transparent background color */

        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
            font-weight: 700;
            color:black;
           
        }

        th {
            font-weight: bold;
            color: white;
            background-color: black;
            box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.2);
        }

        input {
            padding: 6px;
            border-radius: 15px 5px;
        }

        body {
            background-image: url(sunflower.jpg);
            background-repeat: no-repeat;
            background-size: cover;
            background-position:center;
        }
    </style>
</head>
<body>


    <div class="card1">
        <form action="" method="post" id="weatherForm">
            <h1 style="color:white;">Past 7 Days weather </h1>
            <hr>
            <br>
            
            <!-- This is an HTML label tag to display a label for the input element. -->
            <label for="city_name" style="color:white; font-weight:700;">Enter City Name:</label>
            <input type="text" name="city_name" id="cityInput" value="Mesa">
                
            <button type="submit">
             
                <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 1024 1024" height="1.5em" width="1.5em" xmlns="http://www.w3.org/2000/svg">
                    <path d="M909.6 854.5L649.9 594.8C690.2 542.7 712 479 712 412c0-80.2-31.3-155.4-87.9-212.1-56.6-56.7-132-87.9-212.1-87.9s-155.5 31.3-212.1 87.9C143.2 256.5 112 331.8 112 412c0 80.1 31.3 155.5 87.9 212.1C256.5 680.8 331.8 712 412 712c67 0 130.6-21.8 182.7-62l259.7 259.6a8.2 8.2 0 0 0 11.6 0l43.6-43.5a8.2 8.2 0 0 0 0-11.6zM570.4 570.4C528 612.7 471.8 636 412 636s-116-23.3-158.4-65.6C211.3 528 188 471.8 188 412s23.3-116.1 65.6-158.4C296 211.3 352.2 188 412 188s116.1 23.2 158.4 65.6S636 352.2 636 412s-23.3 116.1-65.6 158.4z"></path>
                </svg>
            </button> 
        </form>
    </div>

<?php
    $servername = "sql304.epizy.com";
    $username = "epiz_34251176";
    $password = "8xWZQQdddaxF";
    $dbname = "epiz_34251176_database";
    $conn = mysqli_connect($servername, $username, $password , $dbname);
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // This code block sets the value of $cty_name to user input or a default city name.
    if(isset($_POST['city_name'])){
        $cty_name = $_POST['city_name'];
    } else {
        $cty_name = 'Mesa'; // default city name
    }

    $start_timestamp = strtotime('-7 days 12:00:00'); // start from 7 days ago at 12 pm
    $current_timestamp = strtotime('now');
    while ($start_timestamp < $current_timestamp) {
        $start_date = date('Y-m-d', $start_timestamp);
        $datas = json_decode(file_get_contents("https://history.openweathermap.org/data/2.5/history/city?q=".$cty_name.",&type=hour&start=".$start_timestamp."&cnt=24&appid=faf337b47999f8244054ad8ba8ea6159"), true);
        if (!empty($datas)) {
            mysqli_query($conn, "DELETE FROM weatherapp WHERE city = '{$cty_name}' AND date = '{$start_date}'"); // delete old data for the given date
            foreach ($datas['list'] as $data) {
                $unix_timestamp = $data['dt'];
                $date_time = date("Y-m-d H:i:s", $unix_timestamp);
                $current_time = date("H:i:s", $unix_timestamp);
                $full_date = date("Y-m-d", $unix_timestamp);
                if ($current_time === '12:00:00') { // check if the current time is 12 pm
                    $temperature_celsius = round($data['main']['temp'] - 273.15, 2); // Convert temperature to Celsius
                    $sql = "INSERT INTO weatherapp (temperature, pressure, humidity, wind, date, city)
                            VALUES ('{$temperature_celsius}', '{$data['main']['pressure']}', '{$data['main']['humidity']}', '{$data['wind']['speed']}', '{$full_date}', '{$cty_name}')";
                    mysqli_query($conn, $sql);
                }
            }
        }
        
        $start_timestamp = strtotime('+1 day', $start_timestamp); // move to the next day
    }
    $getdata_sql = "SELECT city, DATE_FORMAT(date, '%Y-%m-%d') AS full_date, AVG(temperature) AS temperature_avg, AVG(pressure) AS pressure_avg, AVG(humidity) AS humidity_avg, AVG(wind) AS wind_avg
                    FROM weatherapp
                    WHERE city = '{$cty_name}'
                    GROUP BY city, full_date
                    ORDER BY full_date DESC";
    $req_all_data = mysqli_query($conn, $getdata_sql);
    echo "<h2 style='color: white;'>Weather Data for <span style='color: yellow;'>$cty_name</span></h2>";

    if ($req_all_data) {
        echo '<table><thead><tr><th>City Name</th><th>Date</th><th>Temperature</th><th>Pressure</th><th>Humidity</th><th>Wind</th></tr></thead><tbody>';

        while ($data = mysqli_fetch_assoc($req_all_data)) {
            $temperature_avg = intval($data['temperature_avg']);
            $pressure_avg = intval($data['pressure_avg']);
            $humidity_avg = intval($data['humidity_avg']);
            $wind_avg = intval($data['wind_avg']);

            echo "<tr><td>{$data['city']}</td><td>{$data['full_date']}</td><td>{$temperature_avg} °C</td><td>{$pressure_avg} hPa</td><td>{$humidity_avg} %</td><td>{$wind_avg} m/s</td></tr>";
        }

        echo '</tbody></table>';
    }

    ?>
    
   
</body>
</html>
