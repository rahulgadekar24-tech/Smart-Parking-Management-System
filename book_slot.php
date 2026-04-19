<?php
session_start();
include("db/config.php");

// 🔐 Restrict access
if(!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

// Prevent caching to ensure logout works securely on 'back' button
header("Cache-Control: no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");

$slot_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if(!$slot_id) {
    header("Location: dashboard.php");
    exit();
}

// Check if slot is available
$slot = $conn->query("SELECT * FROM slots WHERE id=$slot_id AND status='available'")->fetch_assoc();
if(!$slot) {
    echo "<div style='text-align:center; padding: 4rem; font-family:sans-serif;'><h3>Slot not available.</h3><a href='dashboard.php' style='color:blue;'>Go Back</a></div>";
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
            
            $success = "Slot booked successfully!";
            $slot = false; // Prevent showing the form again
        }
    }
}

include("includes/header.php");
?>

<div style="max-width: 500px; margin: 4rem auto;">
    <div class="card">
        <?php if(isset($success)): ?>
            <div style="text-align:center;">
                <div style="width:60px; height:60px; background:var(--success-bg); border-radius:50%; display:flex; align-items:center; justify-content:center; margin: 0 auto 1.5rem;">
                    <svg style="color:var(--success); width:30px; height:30px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                        <polyline points="22 4 12 14.01 9 11.01"></polyline>
                    </svg>
                </div>
                <h2>Booking Confirmed!</h2>
                <p style="color:var(--ink-mute); margin-bottom:2rem;">Your slot has been successfully booked.</p>
                <a href="dashboard.php" class="btn btn-primary" style="width:100%;">Return to Dashboard</a>
            </div>
        <?php else: ?>
            <div style="text-align:center; margin-bottom: 2rem;">
                <div style="display:inline-flex; align-items:center; justify-content:center; width:48px; height:48px; background:var(--accent-light); border-radius:var(--radius-sm); margin-bottom:1rem;">
                    <svg style="color:var(--accent); width:24px; height:24px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                </div>
                <h2>Book Slot <?php echo htmlspecialchars($slot['slot_number']); ?></h2>
                <p style="color:var(--ink-mute);">Choose when you want to park</p>
            </div>

            <?php if(isset($error)) { echo "<div class='alert alert-error'>$error</div>"; } ?>

            <form method="POST">
                <label for="booking_time" style="font-size:0.85rem; font-weight:600; color:var(--ink-soft); margin-bottom:0.3rem; display:block;">Select Booking Time</label>
                <input type="datetime-local" name="booking_time" id="booking_time" class="input-field" required>
                
                <button type="submit" name="book_slot" class="btn btn-primary" style="width:100%; margin-top:1rem; padding: 0.85rem;">Confirm Booking</button>
                <a href="dashboard.php" class="btn btn-secondary" style="width:100%; margin-top:0.5rem; padding: 0.85rem;">Cancel</a>
            </form>
        <?php endif; ?>
    </div>
</div>

<?php include("includes/footer.php"); ?>
