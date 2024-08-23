<?php
require 'config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$confirmation_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_user_booking'])) {
    $booking_id = $_POST['booking_id'];

    // Delete booking by the logged-in user
    $sql = "DELETE FROM bookings WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $booking_id, $user_id);

    if ($stmt->execute()) {
        $confirmation_message = "Booking deleted!";
    } else {
        $error_message = "Error: " . $stmt->error;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['create_booking'])) {
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];

    // Check if the booking dates conflict with existing bookings
    $sql_check = "SELECT * FROM bookings WHERE 
                  (start_date <= ? AND end_date >= ?) OR
                  (start_date <= ? AND end_date >= ?)";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("ssss", $end_date, $start_date, $start_date, $end_date);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows > 0) {
        $error_message = "Error: Selected dates conflict with existing bookings.";
    } else {
        // Create new booking
        $sql_insert = "INSERT INTO bookings (user_id, start_date, end_date) VALUES (?, ?, ?)";
        $stmt_insert = $conn->prepare($sql_insert);
        $stmt_insert->bind_param("iss", $user_id, $start_date, $end_date);

        if ($stmt_insert->execute()) {
            $confirmation_message = "Booking confirmed!";
        } else {
            $error_message = "Error: " . $stmt_insert->error;
        }
    }
}

// Fetch all bookings
$sql_all = "SELECT b.id, b.start_date, b.end_date, u.first_name, u.last_name 
            FROM bookings b 
            JOIN users u ON b.user_id = u.id";
$result_all = $conn->query($sql_all);

// Fetch bookings for the logged-in user
$sql_user = "SELECT id, start_date, end_date FROM bookings WHERE user_id = ?";
$stmt_user = $conn->prepare($sql_user);
$stmt_user->bind_param("i", $user_id);
$stmt_user->execute();
$result_user = $stmt_user->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bookings</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;300;400;500;700&display=swap" rel="stylesheet">
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
        <h1>Make a booking</h1>
        <p>Let us know when you need the van so we can check its available</p>
        <!-- Booking Form -->
        <form class="booking-form" method="POST" action="booking.php">
            <div class="inner-left">
                <div class="date-picker-container">
                    <label>Date From:</label>
                    <input type="date" name="start_date" required>
                </div>
                <div class="date-picker-container">
                    <label>Date To:</label>
                    <input type="date" name="end_date" required>
                </div>
            </div>
            <button type="submit" name="create_booking">Confirm Booking</button>
        </form>

        <!-- Message Container -->
        <div class="message-container">
            <?php if (!empty($confirmation_message)): ?>
                <p class="confirmation"><?php echo htmlspecialchars($confirmation_message); ?></p>
            <?php endif; ?>
            <?php if (!empty($error_message)): ?>
                <p class="error"><?php echo htmlspecialchars($error_message); ?></p>
            <?php endif; ?>
        </div>

        <!-- Display User's Bookings -->
        <h2>Your Bookings:</h2>
        <div class="user-booking-list">
            <?php while ($row_user = $result_user->fetch_assoc()): ?>
                <div class="user-booking-item">
                    <div>
                        From: <?php echo (new DateTime($row_user['start_date']))->format('d/m/Y'); ?><br>
                        To: <?php echo (new DateTime($row_user['end_date']))->format('d/m/Y'); ?>
                    </div>
                    <form method="POST" action="booking.php" style="display:inline;">
                        <input type="hidden" name="booking_id" value="<?php echo htmlspecialchars($row_user['id']); ?>">
                        <button type="submit" name="delete_user_booking">Delete</button>
                    </form>
                </div>
            <?php endwhile; ?>
        </div>

        <!-- Display All Bookings -->
        <h2>All Bookings:</h2>
        <div class="booking-list">
            <?php while ($row_all = $result_all->fetch_assoc()): ?>
                <div class="booking-item">
                    <div>
                        <strong><?php echo htmlspecialchars($row_all['first_name']) . ' ' . htmlspecialchars($row_all['last_name']); ?></strong><br>
                        From: <?php echo (new DateTime($row_all['start_date']))->format('d/m/Y'); ?><br>
                        To: <?php echo (new DateTime($row_all['end_date']))->format('d/m/Y'); ?>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </main>
</body>
</html>
