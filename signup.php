<?php
include("connection.php");

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $remember_me = isset($_POST['remember_me']) ? 1 : 0;

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // Prepare and bind
    $stmt = $conn->prepare("INSERT INTO users (firstname, lastname, email, password, remember_me, created_at, updated_at) VALUES (?, ?, ?, ?, ?, NOW(), NOW())");
    $stmt->bind_param("ssssi", $firstname, $lastname, $email, $hashed_password, $remember_me);

    // Execute the query
    if ($stmt->execute()) {
        echo "New user successfully registered!";
        echo "<script type='text/javascript'>
            window.location.href = 'elvia.html'; // Change this URL to your desired destination
          </script>";
    exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
}
?>
