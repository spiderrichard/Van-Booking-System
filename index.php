<?php
require 'config.php';
session_start();
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
        <h1>Welcome to the Van Booking System</h1>

        <?php if (isset($_SESSION['user_id'])): ?>
            <p>Hi <?php echo htmlspecialchars($_SESSION['first_name']); ?>, you can make a booking <a href="booking.php">here</a></p>
        <?php else: ?>
            <p><a href="login.php">Login</a> or <a href="signup.php">Sign Up</a></p>
        <?php endif; ?>

    </main>
</body>
</html>
