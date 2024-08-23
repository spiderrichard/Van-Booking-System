<?php
session_start();
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Validate email domain
    if (strpos($email, '@evolutionpowertools.com') === false) {
        $error_message = "Sorry only employees with a Evolution email may create accounts";
    } else {
        // Proceed with creating the account
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insert new user into the database
        $sql = "INSERT INTO users (first_name, last_name, email, password) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $first_name, $last_name, $email, $hashed_password);

        if ($stmt->execute()) {
            // Success message or redirect
            $confirmation_message = "Account created successfully!";
            // Optionally, you can log the user in right after signup
            $_SESSION['user_id'] = $stmt->insert_id;
            $_SESSION['first_name'] = $first_name;
            header("Location: index.php");
            exit();
        } else {
            $error_message = "Error: " . $stmt->error;
        }
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
    <!-- Sign Up Form (only shown if the user is not logged in) -->
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
        <h1>Create an account</h1>

        <p>Already got an account? Login <a href="login.php">here</a>.</p>
        <!-- Display Error Message -->
        <?php if (isset($error_message)): ?>
            <p class="error"><?php echo htmlspecialchars($error_message); ?></p>
        <?php endif; ?>

        <!-- Display Confirmation Message -->
        <?php if (isset($confirmation_message)): ?>
            <p class="confirmation"><?php echo htmlspecialchars($confirmation_message); ?></p>
        <?php endif; ?>
        <?php if (!isset($_SESSION['user_id'])): ?>
            <form class="text-inputs" method="POST" action="signup.php">
                <input type="text" name="first_name" placeholder="First Name" required><br>
                <input type="text" name="last_name" placeholder="Last Name" required><br>
                <input type="email" name="email" placeholder="Evolution Email" required><br>
                <input type="password" name="password" placeholder="Password" required><br>
                <button type="submit">Sign Up</button>
            </form>
        <?php else: ?>
            <p>You are already logged in. <a href="booking.php">Go to booking page</a></p>
        <?php endif; ?>
    </main>
</body>
</html>