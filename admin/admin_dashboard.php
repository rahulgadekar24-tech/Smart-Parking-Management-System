<?php
session_start();
include("../db/config.php");

// 🔐 Restrict access
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: admin_login.php");
    exit();
}

// ❌ Cancel Booking + FREE SLOT
if(isset($_GET['delete'])) {
    $id = $_GET['delete'];

    // get slot id
    $res = mysqli_query($conn, "SELECT slot_id FROM bookings WHERE id=$id");
    $data = mysqli_fetch_assoc($res);
    $slot_id = $data['slot_id'];

    // delete booking
    mysqli_query($conn, "DELETE FROM bookings WHERE id=$id");

    // free slot
    mysqli_query($conn, "UPDATE slots SET status='available' WHERE id=$slot_id");

    header("Location: admin_dashboard.php");
    exit();
}

// 📊 STATS
$total_slots = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM slots"))['total'];

$available_slots = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM slots WHERE status='available'"))['total'];

$booked_slots = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM slots WHERE status='occupied'"))['total'];
?>

<?php include("../includes/header.php"); ?>

<div class="dashboard">

    <!-- TOP BAR -->
    <div class="admin-topbar">
        <h2>Admin Dashboard</h2>

        <div>
            <a href="admin_slots.php" class="admin-btn">Manage Slots</a>
            <a href="../logout.php" class="admin-btn admin-logout">Logout</a>
        </div>
    </div>

    <!-- 📊 STATS CARDS -->
    <div class="stats-container">

        <div class="stat-card">
            <h3><?php echo $total_slots; ?></h3>
            <p>Total Slots</p>
        </div>

        <div class="stat-card available">
            <h3><?php echo $available_slots; ?></h3>
            <p>Available</p>
        </div>

        <div class="stat-card occupied">
            <h3><?php echo $booked_slots; ?></h3>
            <p>Booked</p>
        </div>

    </div>

    <!-- BOOKINGS TABLE -->
    <div class="admin-card">

        <h3 class="table-title">Bookings</h3>

        <table class="admin-table">
            <thead>
                <tr>
                    <th>Slot</th>
                    <th>User ID</th>
                    <th>Booking Time</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>
                <?php
                $result = $conn->query("
                    SELECT bookings.*, slots.slot_number 
                    FROM bookings 
                    JOIN slots ON bookings.slot_id = slots.id
                ");

                if($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                ?>
                <tr>
                    <td><?php echo $row['slot_number']; ?></td>
                    <td><?php echo $row['user_id']; ?></td>
                    <td><?php echo date("d M Y, h:i A", strtotime($row['booking_time'])); ?></td>
                    <td>
                        <a href="admin_dashboard.php?delete=<?php echo $row['id']; ?>" 
                           class="cancel-btn"
                           onclick="return confirm('Cancel this booking?')">
                           Cancel
                        </a>
                    </td>
                </tr>
                <?php 
                    }
                } else {
                    echo "<tr><td colspan='4'>No bookings found</td></tr>";
                }
                ?>
            </tbody>
        </table>

    </div>

</div>

<?php include("../includes/footer.php"); ?>