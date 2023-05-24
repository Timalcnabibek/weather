// Function to fetch weather data and update the DOM
function fetchWeatherData(city) {
    // Make an HTTP request to fetch the weather data from the PHP file
    fetch(`getWeatherData.php?city=${city}`)
      .then(response => response.json())
      .then(data => {
        // Extract the weather data and manipulate the DOM to display it
        const weatherDataElement = document.getElementById('weatherData');
        let html = '<table>';
        html += '<tr><th>Date</th><th>City</th><th>Temperature</th><th>Humidity</th><th>Wind Speed</th><th>Pressure</th></tr>';
  
        if (Array.isArray(data)) {
          // Loop through the weather data and generate table rows
          data.forEach(weather => {
            html += '<tr>';
            html += `<td>${weather.date}</td>`;
            html += `<td>${weather.city}</td>`;
            html += `<td>${weather.temperature}</td>`;
            html += `<td>${weather.humidity}</td>`;
            html += `<td>${weather.windSpeed}</td>`;
            html += `<td>${weather.pressure}</td>`;
            html += '</tr>';
          });
        } else {
          html += '<tr><td colspan="6">No weather data found.</td></tr>';
        }
  
        html += '</table>';
  
        // Set the generated HTML content to the weatherDataElement
        weatherDataElement.innerHTML = html;
      })
      .catch(error => console.log('Error:', error));
  }
  
  // Store the last searched city in localStorage when the form is submitted
  document.querySelector('form').addEventListener('submit', function(event) {
    event.preventDefault();
  
    const city = document.getElementById('cityInput').value;
  
    // Store the city in localStorage
    localStorage.setItem('lastSearchedCity', city);
  
    // Fetch weather data for the searched city
    fetchWeatherData(city);
  });
  
  // Retrieve the last searched city from localStorage
  const lastSearchedCity = localStorage.getItem('lastSearchedCity');
  const cityInput = document.getElementById('cityInput');
  
  // Set the value of the city input field to the last searched city
  if (lastSearchedCity) {
    cityInput.value = lastSearchedCity;
    fetchWeatherData(lastSearchedCity);
  } else {
    fetchWeatherData('Mesa'); // Fetch default weather data for Mesa
  }
  

  