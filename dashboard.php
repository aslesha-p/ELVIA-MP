<?php
// Start the session to access logged-in user info
session_start();

// Check if the user is logged in (i.e., if user_id is set in the session)
if (!isset($_SESSION['user_id'])) {
    // If not logged in, redirect to login page
    header("Location: login.php");
    exit();
}

// Include the database connection file
require_once 'connection.php';

// Get the user_id from the session (user_id should be set upon successful login)
$user_id = $_SESSION['user_id'];

// Prepare a SQL query to fetch user data based on the user_id
$stmt = $conn->prepare("SELECT firstname, lastname, email, created_at FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id); // 'i' means it's an integer (user_id)

$stmt->execute();
$stmt->store_result();

// Check if the user exists
if ($stmt->num_rows > 0) {
    // Bind result variables
    $stmt->bind_result($firstname, $lastname, $email, $created_at);

    // Fetch the data
    $stmt->fetch();
} else {
    // If user is not found (which shouldn't happen), redirect to the login page
    header("Location: login.php");
    exit();
}

// Close the prepared statement
$stmt->close();

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="dashboard.css">
    <title>Dashboard | Ludiflex</title>
</head>
<body>
    <div class="wrapper">
        <nav class="nav">
            <div class="nav-logo">
                <p>ELVIA: Manage Your EV</p>
            </div>
            <div class="nav-menu" id="navMenu">
                <ul>
                    <li><a href="dashboard.php" class="link active">Dashboard</a></li>
                    <li><a href="profile.php" class="link">Profile</a></li>
                    <li><a href="logout.php" class="link">Logout</a></li>
                </ul>
            </div>
        </nav>

        <div class="dashboard-container">
            <header>Welcome, <?php echo htmlspecialchars($firstname . ' ' . $lastname); ?>!</header>
            
            <div class="profile-info">
                <h2>Your Profile Information</h2>
                <p><strong>Full Name:</strong> <?php echo htmlspecialchars($firstname . ' ' . $lastname); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>
                <p><strong>Account Created On:</strong> <?php echo htmlspecialchars(date("F j, Y, g:i a", strtotime($created_at))); ?></p>
            </div>
        </div>
    </div>

    <script>
        // Add your menu toggle logic if necessary
        function myMenuFunction() {
            var i = document.getElementById("navMenu");

            if (i.className === "nav-menu") {
                i.className += " responsive";
            } else {
                i.className = "nav-menu";
            }
        }
    </script>
</body>
</html>
