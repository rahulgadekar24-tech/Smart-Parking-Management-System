<?php
session_start();
include("db/config.php");

if(!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

// Handle booking cancellation
if(isset($_GET['cancel'])) {
    $booking_id = (int)$_GET['cancel'];

    // Determine user id
    $user_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;
    if($user_id === 0 && !empty($_SESSION['user_email'])) {
        $email = mysqli_real_escape_string($conn, $_SESSION['user_email']);
        $user = $conn->query("SELECT * FROM users WHERE email='$email'")->fetch_assoc();
        $user_id = $user['id'] ?? 0;
    }

    if($booking_id > 0 && $user_id > 0) {
        // Verify booking belongs to user
        $booking = $conn->query("SELECT * FROM bookings WHERE id=$booking_id AND user_id=$user_id")->fetch_assoc();
        if($booking) {
            $slot_id = $booking['slot_id'];

            // Delete booking
            $conn->query("DELETE FROM bookings WHERE id=$booking_id");

            // Free slot
            $conn->query("UPDATE slots SET status='available' WHERE id=$slot_id");

            header("Location: dashboard.php");
            exit();
        }
    }
}

include("includes/header.php");
?>

<div class="dashboard">

    <!-- TOP BAR -->
    <div class="admin-topbar">
        <h2>Welcome, <?php echo $_SESSION['user']; ?> 👋</h2>

        <div>
            <?php if(isset($_SESSION['role']) && $_SESSION['role'] == 'admin') { ?>
                <a href="admin/admin_dashboard.php" class="admin-btn">Admin Panel</a>
            <?php } ?>

            <a href="logout.php" class="logout-btn">Logout</a>
        </div>
    </div>

    <h3 style="margin-top:20px;">Available Parking Slots</h3>

    <!-- SLOT GRID -->
    <div class="slot-container">

        <?php
        $result = $conn->query("SELECT * FROM slots");

        while($row = $result->fetch_assoc()) {
        ?>
            <div class="slot-card <?php echo $row['status']; ?>">
                
                <h3><?php echo $row['slot_number']; ?></h3>

                <?php if($row['status'] == 'available') { ?>
                    <a href="book_slot.php?id=<?php echo $row['id']; ?>" class="book-btn">Book Now</a>
                <?php } else { ?>
                    <p class="occupied-text">Occupied</p>
                <?php } ?>

            </div>
        <?php } ?>

    </div>

    <!-- USER'S BOOKINGS -->
    <div class="admin-card" style="margin-top:40px;">
        <h3 class="table-title">Your Bookings</h3>

        <table class="admin-table">
            <thead>
                <tr>
                    <th>Slot</th>
                    <th>Booking Time</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $user_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;
                if($user_id > 0) {
                    $result = $conn->query("
                        SELECT bookings.*, slots.slot_number 
                        FROM bookings 
                        JOIN slots ON bookings.slot_id = slots.id 
                        WHERE bookings.user_id = $user_id
                        ORDER BY bookings.booking_time DESC
                    ");

                    if($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['slot_number']); ?></td>
                    <td><?php echo date("d M Y, h:i A", strtotime($row['booking_time'])); ?></td>
                    <td>
                        <a href="dashboard.php?cancel=<?php echo $row['id']; ?>" 
                           class="cancel-btn"
                           onclick="return confirm('Cancel this booking?')">
                           Cancel
                        </a>
                    </td>
                </tr>
                <?php 
                        }
                    } else {
                        echo "<tr><td colspan='3'>No bookings found</td></tr>";
                    }
                } else {
                    echo "<tr><td colspan='3'>No bookings found</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

</div>

<?php include("includes/footer.php"); ?>
