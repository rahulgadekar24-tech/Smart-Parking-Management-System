<?php
session_start();
include("db/config.php");

if(!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$slot_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if(!$slot_id) {
    header("Location: dashboard.php");
    exit();
}

// Check if slot is available
$slot = $conn->query("SELECT * FROM slots WHERE id=$slot_id AND status='available'")->fetch_assoc();
if(!$slot) {
    echo "Slot not available. <a href='dashboard.php'>Go Back</a>";
    exit();
}

// Handle booking
if(isset($_POST['book_slot'])) {
    $booking_time_raw = $_POST['booking_time'];
    $booking_timestamp = strtotime($booking_time_raw);

    // Validate booking time (must be in future)
    if(!$booking_timestamp || $booking_timestamp <= time()) {
        $error = "Please select a future time.";
    } else {
        $booking_time = date("Y-m-d H:i:s", $booking_timestamp);

        // Get user id
        $user_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;
        if(!$user_id) {
            $email = $_SESSION['user_email'] ?? '';
            $user = $conn->query("SELECT * FROM users WHERE email='$email'")->fetch_assoc();
            $user_id = $user['id'] ?? 0;
        }

        if(!$user_id) {
            $error = "Unable to determine user account.";
        } else {
            // Insert booking
            $conn->query("INSERT INTO bookings (user_id, slot_id, booking_time) VALUES ($user_id, $slot_id, '$booking_time')");

            // Update slot status
            $conn->query("UPDATE slots SET status='occupied' WHERE id=$slot_id");

            echo "Slot booked successfully! <a href='dashboard.php'>Go Back</a>";
            exit();
        }
    }
}

include("includes/header.php");
?>

<div class="card" style="margin:auto; margin-top:50px; max-width:500px;">
    <h2>Book Slot: <?php echo htmlspecialchars($slot['slot_number']); ?></h2>

    <?php if(isset($error)) { echo "<p style='color:red;'>$error</p>"; } ?>

    <form method="POST">
        <label for="booking_time">Select Booking Time:</label><br>
        <input type="datetime-local" name="booking_time" id="booking_time" required style="width:100%; padding:10px; margin:10px 0;"><br><br>
        <button type="submit" name="book_slot">Book Slot</button>
    </form>
</div>

<?php include("includes/footer.php"); ?>
