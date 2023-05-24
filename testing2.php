<?php
$api_key = 'tRgugnr7csovpdQY5Zfm1TzPEtjlY6JR';
$location = 'mesa';

// Create the API URL
$url = "https://api.tomorrow.io/v4/timelines?location=40.75872069597532,-73.98529171943665&fields=temperature&timesteps=1h&units=metric&apikey=tRgugnr7csovpdQY5Zfm1TzPEtjlY6JR";

// Initialize cURL
$ch = curl_init($url);

// Set options
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// Execute the API request
$response = curl_exec($ch);

// Get the HTTP status code
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

// Close cURL
curl_close($ch);

// Check if the API request was successful
if ($httpCode == 200) {
    echo "API key is valid.";
    // Process the response data
    // ...
} else {
    echo "API key is not valid.";
}
?>
