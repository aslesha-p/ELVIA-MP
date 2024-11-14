<?php
// Database credentials
$host = 'localhost';       // Database host (usually 'localhost')
$username = 'root';        // Database username
$password = '';            // Database password (default is usually empty for local dev)
$database = 'ev'; // Database name

// Create a connection
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

//echo "Connected successfully!";
?>
