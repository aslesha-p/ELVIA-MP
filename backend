const express = require('express');
const mongoose = require('mongoose');
const bodyParser = require('body-parser');

const app = express();

// Connect to MongoDB
mongoose.connect('mongodb://localhost/electricVehicles', { useNewUrlParser: true, useUnifiedTopology: true });

// Define the charging stations model
const chargingStationSchema = new mongoose.Schema({
  name: String,
  location: {
    type: {
      type: String,
      enum: ['Point'],
      required: true
    },
    coordinates: {
      type: [Number],
      required: true
    }
  },
  address: String
});

const ChargingStation = mongoose.model('ChargingStation', chargingStationSchema);

// Define the vehicles model
const vehicleSchema = new mongoose.Schema({
  make: String,
  model: String,
  price: Number,
  range: Number
});

const Vehicle = mongoose.model('Vehicle', vehicleSchema);

// Create API endpoints

// Get nearest charging stations
app.get('/api/chargingStations/near', (req, res) => {
  const lat = req.query.lat;
  const lon = req.query.lon;
  const maxDistance = 10; // in miles

  ChargingStation.find({
    location: {
      $near: {
        $geometry: {
          type: 'Point',
          coordinates: [lon, lat]
        },
        $maxDistance: maxDistance * 1609.34 // convert miles to meters
      }
    }
  })
  .then(chargingStations => res.json(chargingStations))
  .catch(err => res.status(500).json({ message: 'Error finding charging stations' }));
});

// Get vehicles by budget
app.get('/api/vehicles/byBudget', (req, res) => {
  const minPrice = req.query.minPrice;
  const maxPrice = req.query.maxPrice;

  Vehicle.find({
    price: {
      $gte: minPrice,
      $lte: maxPrice
    }
  })
  .then(vehicles => res.json(vehicles))
  .catch(err => res.status(500).json({ message: 'Error finding vehicles' }));
});

// Start the server
const port = 3000;
app.listen(port, () => console.log(`Server started on port ${port}`));



// Get nearest charging stations
app.get('/api/chargingStations/near', (req, res) => {
  const lat = req.query.lat;
  const lon = req.query.lon;
  const maxDistance = 10; // in miles

  ChargingStation.find({
    location: {
      $near: {
        $geometry: {
          type: 'Point',
          coordinates: [lon, lat]
        },
        $maxDistance: maxDistance * 1609.34 // convert miles to meters
      }
    }
  })
  .then(chargingStations => {
    // Calculate the distance from the user's location to each charging station
    chargingStations.forEach(chargingStation => {
      const distance = calculateDistance(lat, lon, chargingStation.location.coordinates[1], chargingStation.location.coordinates[0]);
      chargingStation.distance = distance;
    });

    // Sort the charging stations by distance
    chargingStations.sort((a, b) => a.distance - b.distance);

    res.json(chargingStations);8
  })
  .catch(err => res.status(500).json({ message: 'Error finding charging stations' }));
});

// Calculate the distance between two points using the Haversine formula
function calculateDistance(lat1, lon1, lat2, lon2) {
  const R = 6371; // radius of the Earth in kilometers
  const dLat = (lat2 - lat1) * Math.PI / 180;
  const dLon = (lon2 - lon1) * Math.PI / 180;
  const lat1Rad = lat1 * Math.PI / 180;
  const lat2Rad = lat2 * Math.PI / 180;

  const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
            Math.sin(dLon / 2) * Math.sin(dLon / 2) * Math.cos(lat1Rad) * Math.cos(lat2Rad);
  const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
  const distance = R * c;

  return distance;
}


// Get vehicles by budget
app.get('/api/vehicles/byBudget', (req, res) => {
  const minPrice = req.query.minPrice;
  const maxPrice = req.query.maxPrice;

  Vehicle.find({
    price: {
      $gte: minPrice,
      $lte: maxPrice
    }
  })
  .then(vehicles => {
    // Filter the vehicles by range (optional)
    if (req.query.minRange && req.query.maxRange) {
      vehicles = vehicles.filter(vehicle => {
        return vehicle.range >= req.query.minRange && vehicle.range <= req.query.maxRange;
      });
    }

    res.json(vehicles);
  })
  .catch(err => res.status(500).json({ message: 'Error finding vehicles' }));
});




//user authentication 

const mongoose = require('mongoose');
const bcrypt = require('bcrypt');

const userSchema = new mongoose.Schema({
  username: String,
  password: String,
  email: String
});

userSchema.pre('save', function(next) {
  const user = this;
  bcrypt.hash(user.password, 10, function(err, hash) {
    if (err) {
      return next(err);
    }
    user.password = hash;
    next();
  });
});

const User = mongoose.model('User', userSchema);

module.exports = User;

const passport = require('passport');
const LocalStrategy = require('passport-local').Strategy;
const User = require('./models/User');

passport.use(new LocalStrategy({
  usernameField: 'username',
  passwordField: 'password'
}, function(username, password, done) {
  User.findOne({ username: username }, function(err, user) {
    if (err) {
      return done(err);
    }
    if (!user) {
      return done(null, false, { message: 'Invalid username or password' });
    }
    bcrypt.compare(password, user.password, function(err, isMatch) {
      if (err) {
        return done(err);
    }
    if (!isMatch) {
      return done(null, false, { message: 'Invalid username or password' });
    }
    return done(null, user);
  });
});
}));

passport.serializeUser(function(user, done) {
  done(null, user.id);
});

passport.deserializeUser(function(id, done) {
  User.findById(id, function(err, user) {
    done(err, user);
  });
});

const express = require('express');
const passport = require('passport');
const session = require('express-session');

const app = express();

app.use(session({
  secret: 'secret',
  resave: false,
  saveUninitialized: true
}));

app.use(passport.initialize());
app.use(passport.session());

// Authentication endpoints
app.post('/api/login', passport.authenticate('local'), function(req, res) {
  res.json({ message: 'Logged in successfully' });
});

app.post('/api/register', function(req, res) {
  const user = new User({
    username: req.body.username,
    password: req.body.password,
    email: req.body.email
  });
  user.save(function(err) {
    if (err) {
      return res.status(500).json({ message: 'Error registering user' });
    }
    res.json({ message: 'Registered successfully' });
  });
});

app.get('/api/logout', function(req, res) {
  req.logout();
  res.json({ message: 'Logged out successfully' });
});


 // Vehicle comparison endpoint
app.post('/api/compareVehicles', function(req, res) {
  const user = req.user;
  const vehicles = req.body.vehicles;

  const vehicleComparison = new VehicleComparison({
    user: user.id,
    vehicles: vehicles
  });



// vehicle comparison 
  vehicleComparison.save(function(err) {
    if (err) {
      return res.status(500).json({ message: 'Error comparing vehicles' });
    }
    res.json({ message: 'Vehicles compared successfully' });
  });
});

app.get('/api/compareVehicles', function(req, res) {
  const user = req.user;

  VehicleComparison.findOne({ user: user.id }, function(err, vehicleComparison) {
    if (err) {
      return res.status(500).json({ message: 'Error retrieving vehicle comparison' });
    }
    if (!vehicleComparison) {
      return res.json({ message: 'No vehicle comparison found' });
    }
    res.json(vehicleComparison);
  });
});

// Vehicle comparison endpoint
app.post('/api/compareVehicles', function(req, res) {
  const user = req.user;
  const vehicles = req.body.vehicles;

  const vehicleComparison = new VehicleComparison({
    user: user.id,
    vehicles: vehicles
  });

  vehicleComparison.save(function(err) {
    if (err) {
      return res.status(500).json({ message: 'Error comparing vehicles' });
    }
    res.json({ message: 'Vehicles compared successfully' });
  });
});

app.get('/api/compareVehicles', function(req, res) {
  const user = req.user;

  VehicleComparison.findOne({ user: user.id }, function(err, vehicleComparison) {
    if (err) {
      return res.status(500).json({ message: 'Error retrieving vehicle comparison' });
    }
    if (!vehicleComparison) {
      return res.json({ message: 'No vehicle comparison found' });
    }
    res.json(vehicleComparison);
  });
});




//code to connect front end and backend 
// Import the JavaScript code
import { api } from './api.js';

// Get the content element
const contentElement = document.getElementById('content');

// Render the content
api.getChargingStations().then(chargingStations => {
    const chargingStationsHTML = chargingStations.map(chargingStation => {
        return `
            <h2>${chargingStation.name}</h2>
            <p>Location: ${chargingStation.location}</p>
            <p>Address: ${chargingStation.address}</p>
        `;
    }).join('');
    contentElement.innerHTML = chargingStationsHTML;
});

// Add event listeners for navigation
document.querySelectorAll('nav a').forEach(link => {
    link.addEventListener('click', event => {
        event.preventDefault();
        const href = link.getAttribute('href');
        switch (href) {
            case '#':
                // Render home page content
                break;
            case '#charging-stations':
                api.getChargingStations().then(chargingStations => {
                    const chargingStationsHTML = chargingStations.map(chargingStation => {
                        return `
                            <h2>${chargingStation.name}</h2>
                            <p>Location: ${chargingStation.location}</p>
                            <p>Address: ${chargingStation.address}</p>
                        `;
                    }).join('');
                    contentElement.innerHTML = chargingStationsHTML;
                });
                break;
            case '#vehicles':
                // Render vehicles page content
                break;
            case '#compare-vehicles':
                // Render compare vehicles page content
                break;
            case '#login':
                // Render login page content
                break;
            case '#register':
                // Render register page content
                break;
        }
    });
});

// Import the API endpoints
import { getChargingStations, getVehicles, compareVehicles, login, register } from './app.js';

// Create an API object
const api = {
    getChargingStations: getChargingStations,
    getVehicles: getVehicles,
    compareVehicles: compareVehicles,
    login: login,
    register: register
};

export default api;











