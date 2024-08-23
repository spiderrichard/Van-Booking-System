<?php
require 'config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$first_name = $_SESSION['first_name'];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_account'])) {
    // Start a transaction
    $conn->begin_transaction();

    try {
        // Delete user's bookings
        $sql_delete_bookings = "DELETE FROM bookings WHERE user_id = ?";
        $stmt_delete_bookings = $conn->prepare($sql_delete_bookings);
        $stmt_delete_bookings->bind_param("i", $user_id);
        $stmt_delete_bookings->execute();

        // Delete user account
        $sql_delete_user = "DELETE FROM users WHERE id = ?";
        $stmt_delete_user = $conn->prepare($sql_delete_user);
        $stmt_delete_user->bind_param("i", $user_id);
        $stmt_delete_user->execute();

        // Commit the transaction
        $conn->commit();

        // Log the user out and redirect to index
        session_unset();
        session_destroy();
        header("Location: index.php");
        exit();
    } catch (Exception $e) {
        // Rollback the transaction if something went wrong
        $conn->rollback();
        $error_message = "Error: " . $e->getMessage();
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
            <?php endif; ?>
        </div>
    </nav>
    <main>
        <h1>Delete Account</h1>
        <p>Hi <?php echo htmlspecialchars($first_name); ?>, would you like to delete your account?</p>

        <!-- Delete Account Form -->
        <form method="POST" action="delete_account.php">
            <button type="submit" name="delete_account" onclick="return confirm('Are you sure you want to delete your account? This will remove your details and all bookings.');">
                Delete My Account
            </button>
        </form>

        <!-- Display Error Message -->
        <?php if (isset($error_message)): ?>
            <p class="error"><?php echo htmlspecialchars($error_message); ?></p>
        <?php endif; ?>
    </main>
</body>
</html>
