// Define API endpoint base URL (Change this to match your deployed API server URL)
const API_BASE_URL = 'http://localhost:3000/api';  // Change to your actual server URL if deployed

// Get the content element where we will display API data
const contentElement = document.getElementById('content');

// Event listener to handle the navigation and API requests
document.querySelectorAll('nav a').forEach(link => {
    link.addEventListener('click', event => {
        event.preventDefault();
        const href = link.getAttribute('href');

        // Load content based on navigation link
        if (href === '#charging-stations') {
            loadChargingStations();
        } else if (href === '#vehicles') {
            loadVehicles();
        } else if (href === '#login') {
            displayLoginForm();
        } else if (href === '#register') {
            displayRegisterForm();
        }
    });
});

// Function to fetch and display charging stations
function loadChargingStations() {
    const lat = 40.7128; // Example latitude (New York City)
    const lon = -74.0060; // Example longitude (New York City)
    
    fetch(`${API_BASE_URL}/chargingStations/near?lat=${lat}&lon=${lon}`)
        .then(response => response.json())
        .then(data => {
            const chargingStationsHTML = data.map(station => {
                return `
                    <div class="charging-station">
                        <h3>${station.name}</h3>
                        <p>Address: ${station.address}</p>
                        <p>Location: Latitude: ${station.location.coordinates[1]}, Longitude: ${station.location.coordinates[0]}</p>
                    </div>
                `;
            }).join('');
            contentElement.innerHTML = chargingStationsHTML;
        })
        .catch(error => {
            contentElement.innerHTML = `<p>Error fetching charging stations: ${error}</p>`;
        });
}

// Function to fetch and display vehicles
function loadVehicles() {
    const minPrice = 20000;
    const maxPrice = 50000;

    fetch(`${API_BASE_URL}/vehicles/byBudget?minPrice=${minPrice}&maxPrice=${maxPrice}`)
        .then(response => response.json())
        .then(data => {
            const vehiclesHTML = data.map(vehicle => {
                return `
                    <div class="vehicle">
                        <h3>${vehicle.make} ${vehicle.model}</h3>
                        <p>Price: $${vehicle.price}</p>
                        <p>Range: ${vehicle.range} miles</p>
                    </div>
                `;
            }).join('');
            contentElement.innerHTML = vehiclesHTML;
        })
        .catch(error => {
            contentElement.innerHTML = `<p>Error fetching vehicles: ${error}</p>`;
        });
}

// Function to display login form
function displayLoginForm() {
    document.getElementById('loginForm').style.display = 'block';
    document.getElementById('registerForm').style.display = 'none';
    contentElement.innerHTML = ''; // Clear content
}

// Function to display register form
function displayRegisterForm() {
    document.getElementById('registerForm').style.display = 'block';
    document.getElementById('loginForm').style.display = 'none';
    contentElement.innerHTML = ''; // Clear content
}

// Handle user login
document.getElementById('loginFormElement')?.addEventListener('submit', function (event) {
    event.preventDefault();
    
    const username = document.getElementById('username').value;
    const password = document.getElementById('password').value;
    
    fetch(`${API_BASE_URL}/login`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ username, password })
    })
    .then(response => response.json())
    .then(data => {
        if (data.message === 'Logged in successfully') {
            contentElement.innerHTML = '<p>Login successful!</p>';
        } else {
            contentElement.innerHTML = `<p>Login failed: ${data.message}</p>`;
        }
    })
    .catch(error => contentElement.innerHTML = `<p>Login error: ${error}</p>`);
});

// Handle user registration
document.getElementById('registerFormElement')?.addEventListener('submit', function (event) {
    event.preventDefault();
    
    const username = document.getElementById('registerUsername').value;
    const email = document.getElementById('registerEmail').value;
    const password = document.getElementById('registerPassword').value;
    
    fetch(`${API_BASE_URL}/register`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ username, email, password })
    })
    .then(response => response.json())
    .then(data => {
        contentElement.innerHTML = `<p>${data.message}</p>`;
    })
    .catch(error => contentElement.innerHTML = `<p>Registration error: ${error}</p>`);
});
