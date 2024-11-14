<?php
// Include database connection file
require_once 'connection.php';

// Start session to store user login status
session_start();

// Check if the login form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['email']) && isset($_POST['password'])) {
    // Get form inputs
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare and bind
    $stmt = $conn->prepare("SELECT id, firstname, lastname, email, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);  // 's' means it's a string

    // Execute query
    $stmt->execute();

    // Store result
    $stmt->store_result();

    // Check if the user exists in the database
    if ($stmt->num_rows > 0) {
        // Bind result variables
        $stmt->bind_result($user_id, $firstname, $lastname, $email_db, $password_db);

        // Fetch the data
        $stmt->fetch();

        // Verify password
        if (password_verify($password, $password_db)) {
            // Password is correct, login successful
            $_SESSION['user_id'] = $user_id;
            $_SESSION['firstname'] = $firstname;
            $_SESSION['lastname'] = $lastname;
            $_SESSION['email'] = $email_db;

            // Redirect to the dashboard or home page
            header("Location: elvia.html");  // Change this to your actual landing page
            exit();
        } else {
            // Incorrect password
            $error_message = "Invalid password!";
        }
    } else {
        // User not found
        $error_message = "No account found with that email!";
    }

    // Close the prepared statement
    $stmt->close();
}

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
    <link rel="stylesheet" href="login.css">
    <title>Ludiflex | Login</title>
</head>
<body>
    <div class="wrapper">
        <nav class="nav">
            <div class="nav-logo">
                <p>ELVIA: Manage Your EV .</p>
            </div>
            <div class="nav-menu" id="navMenu">
                <ul>
                    <li><a href="#" class="link active">Home</a></li>
                    <li><a href="#" class="link">Blog</a></li>
                    <li><a href="#" class="link">Services</a></li>
                    <li><a href="#" class="link">About</a></li>
                </ul>
            </div>
            <div class="nav-button">
                <button class="btn white-btn" id="loginBtn" onclick="login()">Sign In</button>
                <button class="btn" id="registerBtn" onclick="register()">Sign Up</button>
            </div>
        </nav>

        <div class="form-box">
            <div class="login-container" id="login">
                <div class="top">
                    <span>Don't have an account? <a href="signup.html" onclick="register()">Sign Up</a></span>
                    <header>Login</header>
                </div>
                <div class="input-box">
                    <form action="login.php" method="POST">
                        <input type="text" class="input-field" name="email" placeholder="Username or Email" required>
                        <i class="bx bx-user"></i>
                    </div>
                    <div class="input-box">
                        <input type="password" class="input-field" name="password" placeholder="Password" required>
                        <i class="bx bx-lock-alt"></i>
                    </div>
                    <div class="input-box">
                        <input type="submit" class="submit" value="Sign In">
                    </div>
                    <div class="two-col">
                        <div class="one">
                            <input type="checkbox" id="login-check" name="remember_me">
                            <label for="login-check"> Remember Me</label>
                        </div>
                        <div class="two">
                            <label><a href="#">Forgot password?</a></label>
                        </div>
                    </div>
                </form>
                <?php
                if (isset($error_message)) {
                    echo "<p style='color: red; text-align: center;'>$error_message</p>";
                }
                ?>
            </div>
        </div>
    </div>  
    <script>
        // Add your menu toggle logic if necessary
        function myMenuFunction() {
            var i = document.getElementById("navMenu");

            if(i.className === "nav-menu") {
                i.className += " responsive";
            } else {
                i.className = "nav-menu";
            }
        }

        var a = document.getElementById("loginBtn");
        var b = document.getElementById("registerBtn");
        var x = document.getElementById("login");
        var y = document.getElementById("register");

        function login() {
            x.style.left = "4px";
            y.style.right = "-520px";
            a.className += " white-btn";
            b.className = "btn";
            x.style.opacity = 1;
            y.style.opacity = 0;
        }

        function register() {
            x.style.left = "-510px";
            y.style.right = "5px";
            a.className = "btn";
            b.className += " white-btn";
            x.style.opacity = 0;
            y.style.opacity = 1;
        }
    </script>
</body>
</html>
