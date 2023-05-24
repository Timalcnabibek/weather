window.addEventListener('load', () => {
        //defines variable CityInput
    const cityInput = document.getElementById('city');
        //sets the value
    cityInput.value = 'Mesa';
  
    getWeather('Mesa');
  });
  
  
  function getWeather(city) {
      // api key
    const apiKey = '5ffb449047c3a28fc1274437369fb9d3';
    //url to fetch data from openweather api 
    const apiUrl = `https://api.openweathermap.org/data/2.5/weather?q=${city}&appid=${apiKey}&units=imperial`;
    
  //fetch data from url of openweather api 
    fetch(apiUrl)
    //returns the data to a json format
        .then(response => response.json())
        //passes the json parse as an argument that contains the code from html
        .then(data => {
          //defining const variables refering from html
            const tempElement = document.getElementById('temp');
            const iconElement = document.querySelector('.temperature img');
            const conditionsElement = document.getElementById('conditions');
            const humidityElement = document.getElementById('humidity');
            const windElement = document.getElementById('wind-speed');
              //updates the text and source of the HTML 
              //elements with the weather data returned from the API.
            tempElement.innerText = Math.round(data.main.temp);
            iconElement.src = `https://openweathermap.org/img/w/${data.weather[0].icon}.png`;
            conditionsElement.innerText = data.weather[0].description;
            humidityElement.innerText = `${data.main.humidity}%`;
            windElement.innerText = `${data.wind.speed} mph`;
        })
        .catch(error => console.log(error)); //logs error if error found during the request
  }
  //Defines a click event listener for the submit button and call the getWeather function with the value of the city input field when the button is clicked.
  const submitButton = document.getElementById('submit');
  submitButton.addEventListener('click', () => {
    const city = document.getElementById('city').value;
    getWeather(city);
  });

  //These selects the HTML element with therir respective classes and ids 
  //and assigns it to the variables.
const dateElement = document.getElementById('date');
const cityInput = document.getElementById('city');
const weatherInfo = document.querySelector('.weather-info');
const temperatureElement = weatherInfo.querySelector('.temperature span');
const conditionsElement = weatherInfo.querySelector('.conditions span');
const humidityElement = weatherInfo.querySelector('.humidity span');
const windSpeedElement = weatherInfo.querySelector('.wind-speed span');
const weatherIconElement = weatherInfo.querySelector('.temperature img');

// Update date 
function updateDate() {
  const now = new Date();
  const options = {
    weekday: 'long',
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  };
  const date = now.toLocaleDateString('en-US', options);
  const time = now.toLocaleTimeString('en-US', { timeStyle: 'short' });
  dateElement.textContent = date;
 
}
setInterval(updateDate, 1000);
