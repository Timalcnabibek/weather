
    // Event handler for form submission
document.getElementById('weatherForm').addEventListener('submit', function (event) {
    event.preventDefault();
    const cityInput = document.getElementById('cityInput');
    const cityName = cityInput.value.trim();

    if (cityName !== '') {
        if (isOnline()) {
            fetchWeatherData(cityName);
        } else {
            const storedData = getStoredWeatherData(cityName);
            if (storedData) {
                displayWeatherData(storedData);
            } else {
                alert("You are currently offline and there is no stored data for the requested city.");
            }
        }
    }
});
// Event handler for form submission
document.getElementById('weatherForm').addEventListener('submit', function (event) {
  event.preventDefault();
  const cityInput = document.getElementById('cityInput');
  const cityName = cityInput.value.trim();

  if (cityName !== '') {
    if (isOnline()) {
      fetchWeatherData(cityName);
    } else {
      const storedData = getStoredWeatherData(cityName);
      if (storedData) {
        displayWeatherData(storedData);
      } else {
        alert("You are currently offline and there is no stored data for the requested city.");
      }
    }
  }
});

// Check if the user is online
function isOnline() {
  return navigator.onLine;
}

// Retrieve weather data from the API
function fetchWeatherData(cityName) {
  // Fetch the weather data from the API and process it
  // ...

  // Once you have the weather data, store it and display it
  const weatherData = {
    cityName: cityName,
    // ... process the weather data ...
  };
  storeWeatherData(cityName, weatherData);
  displayWeatherData(weatherData);
}

// Store weather data in local storage
function storeWeatherData(cityName, weatherData) {
  const storedData = getStoredWeatherData();
  storedData[cityName] = weatherData;
  localStorage.setItem('weatherData', JSON.stringify(storedData));
}

// Retrieve stored weather data from local storage
function getStoredWeatherData() {
  const storedData = localStorage.getItem('weatherData');
  return storedData ? JSON.parse(storedData) : {};
}

// Display weather data
function displayWeatherData(weatherData) {
  // Display the weather data on the page
  // ...
}

// Event listener for online/offline status changes
window.addEventListener('online', handleOnlineStatus);
window.addEventListener('offline', handleOnlineStatus);

// Handle online/offline status changes
function handleOnlineStatus() {
  const cityInput = document.getElementById('cityInput');
  const cityName = cityInput.value.trim();

  if (isOnline()) {
    if (cityName !== '') {
      fetchWeatherData(cityName);
    }
  } else {
    const storedData = getStoredWeatherData(cityName);
    if (storedData) {
      displayWeatherData(storedData);
    } else {
      alert("You are currently offline and there is no stored data for the requested city.");
    }
  }
}

// Initial offline status check
handleOnlineStatus();

