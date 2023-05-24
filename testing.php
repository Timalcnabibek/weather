<?php
 $apiKey = 'da98a262e080e996ea17f7ddef9d765e';
 $cityName = "Mesa"; // get the city name from the form input
 $startDate = time() - (7 * 24 * 60 * 60); // start date 7 days ago
 $endDate = time(); // end date (now)

 $url = "https://api.openweathermap.org/data/2.5/weather?q=$cityName&appid=$apiKey&units=metric";

 
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode == 200) {
    echo "API request was successful. API key and endpoint are working.";
    // Process the response data
    // ...
} else {
    echo "API request failed. Please check your API key and endpoint.";
}
?>
