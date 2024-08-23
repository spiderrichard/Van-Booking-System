<?php
session_start(); // Start session
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare SQL statement to fetch user details
    $sql = "SELECT id, first_name, password FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Verify the password
        if (password_verify($password, $row['password'])) {
            // Store user id and first name in session
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['first_name'] = $row['first_name']; // Store first name in session

            // Redirect to the bookings page or another secure page
            header("Location: index.php"); // Redirect to index.php or wherever needed
            exit();
        } else {
            $error_message = "Invalid email or password.";
        }
    } else {
        $error_message = "Invalid email or password.";
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
</head>
<body>
    <nav>
        <a href="index.php">Home</a>
        <div class="nav-inner-right">
            <ul class="nav-ul">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li><a href="booking.php">Bookings</a></li>
                    <li><a href="delete_account.php">Delete Account</a></li>
                <?php else: ?>
                    <li><a href="login.php">Login</a></li>
                    <li><a href="signup.php">Sign Up</a></li>
                <?php endif; ?>
            </ul>
            <?php if (isset($_SESSION['user_id'])): ?>
                <form method="POST" action="logout.php" style="display:inline;">
                    <button type="submit">Logout</button>
                </form>
            <?php else: ?>
            <?php endif; ?>
        </div>
    </nav>
    <main>

        <!-- Login Form (only shown if the user is not logged in) -->
        <h1>Login to your account</h1>

        <p>If you don't have an account yet you can sign up <a href="signup.php">here</a>.</p>

        <?php if (!isset($_SESSION['user_id'])): ?>
            <form class="text-inputs" method="POST" action="login.php">
                <input type="email" name="email" placeholder="Evolution Email" required><br>
                <input type="password" name="password" placeholder="Password" required><br>
                <button type="submit">Login</button>
            </form>
        <?php else: ?>
            <p>You are already logged in. <a href="booking.php">Go to booking page</a></p>
        <?php endif; ?>
    </main>
</body>
</html>