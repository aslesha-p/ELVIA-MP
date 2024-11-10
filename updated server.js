const express = require('express');
const mongoose = require('mongoose');
const bodyParser = require('body-parser');
const bcrypt = require('bcrypt');
const passport = require('passport');
const session = require('express-session');
const LocalStrategy = require('passport-local').Strategy;

const app = express();

// Middleware
app.use(bodyParser.json());
app.use(session({ secret: 'secret', resave: false, saveUninitialized: true }));
app.use(passport.initialize());
app.use(passport.session());

// Connect to MongoDB
mongoose.connect('mongodb://localhost/electricVehicles', { useNewUrlParser: true, useUnifiedTopology: true });

// Charging Stations Schema
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

// Vehicle Schema
const vehicleSchema = new mongoose.Schema({
  make: String,
  model: String,
  price: Number,
  range: Number
});

const Vehicle = mongoose.model('Vehicle', vehicleSchema);

// User Schema with bcrypt
const userSchema = new mongoose.Schema({
  username: String,
  password: String,
  email: String
});

userSchema.pre('save', function(next) {
  const user = this;
  bcrypt.hash(user.password, 10, function(err, hash) {
    if (err) return next(err);
    user.password = hash;
    next();
  });
});

const User = mongoose.model('User', userSchema);

// Passport Local Strategy
passport.use(new LocalStrategy({
  usernameField: 'username',
  passwordField: 'password'
}, (username, password, done) => {
  User.findOne({ username }, (err, user) => {
    if (err) return done(err);
    if (!user) return done(null, false, { message: 'Invalid username or password' });
    bcrypt.compare(password, user.password, (err, isMatch) => {
      if (err) return done(err);
      if (!isMatch) return done(null, false, { message: 'Invalid username or password' });
      return done(null, user);
    });
  });
}));

passport.serializeUser((user, done) => done(null, user.id));
passport.deserializeUser((id, done) => {
  User.findById(id, (err, user) => done(err, user));
});

// Routes

// Get nearest charging stations
app.get('/api/chargingStations/near', (req, res) => {
  const { lat, lon } = req.query;
  const maxDistance = 10; // miles
  ChargingStation.find({
    location: {
      $near: {
        $geometry: { type: 'Point', coordinates: [lon, lat] },
        $maxDistance: maxDistance * 1609.34 // convert miles to meters
      }
    }
  })
  .then(chargingStations => {
    chargingStations.forEach(chargingStation => {
      const distance = calculateDistance(lat, lon, chargingStation.location.coordinates[1], chargingStation.location.coordinates[0]);
      chargingStation.distance = distance;
    });
    chargingStations.sort((a, b) => a.distance - b.distance);
    res.json(chargingStations);
  })
  .catch(err => res.status(500).json({ message: 'Error finding charging stations' }));
});

// Calculate distance using Haversine formula
function calculateDistance(lat1, lon1, lat2, lon2) {
  const R = 6371; // radius of Earth in kilometers
  const dLat = (lat2 - lat1) * Math.PI / 180;
  const dLon = (lon2 - lon1) * Math.PI / 180;
  const lat1Rad = lat1 * Math.PI / 180;
  const lat2Rad = lat2 * Math.PI / 180;

  const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
            Math.sin(dLon / 2) * Math.sin(dLon / 2) * Math.cos(lat1Rad) * Math.cos(lat2Rad);
  const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
  return R * c; // distance in kilometers
}

// Get vehicles by budget
app.get('/api/vehicles/byBudget', (req, res) => {
  const { minPrice, maxPrice, minRange, maxRange } = req.query;

  Vehicle.find({
    price: { $gte: minPrice, $lte: maxPrice }
  })
  .then(vehicles => {
    if (minRange && maxRange) {
      vehicles = vehicles.filter(vehicle => vehicle.range >= minRange && vehicle.range <= maxRange);
    }
    res.json(vehicles);
  })
  .catch(err => res.status(500).json({ message: 'Error finding vehicles' }));
});

// User authentication
app.post('/api/login', passport.authenticate('local'), (req, res) => {
  res.json({ message: 'Logged in successfully' });
});

app.post('/api/register', (req, res) => {
  const user = new User(req.body);
  user.save((err) => {
    if (err) return res.status(500).json({ message: 'Error registering user' });
    res.json({ message: 'Registered successfully' });
  });
});

app.get('/api/logout', (req, res) => {
  req.logout();
  res.json({ message: 'Logged out successfully' });
});

// Start server
const port = 3000;
app.listen(port, () => console.log(`Server running on port ${port}`));
