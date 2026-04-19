<?php
session_start();
include("../db/config.php");

// 🔐 Restrict access
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: admin_login.php");
    exit();
}

// ➕ Add Slot
if(isset($_POST['add_slot'])) {
    $slot_number = mysqli_real_escape_string($conn, trim($_POST['slot_number']));
    $query = "INSERT INTO slots (slot_number, status) VALUES ('$slot_number','available')";
    if(mysqli_query($conn, $query)) {
        $msg = "Slot added successfully.";
    } else {
        $msg = "Error adding slot. It may already exist.";
    }
}

// ❌ Delete Slot
if(isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    mysqli_query($conn, "DELETE FROM bookings WHERE slot_id=$id");
    mysqli_query($conn, "DELETE FROM slots WHERE id=$id");
    header("Location: admin_slots.php");
    exit();
}
?>

<?php include("../includes/header.php"); ?>

<div class="dashboard">
    <div class="admin-topbar">
        <h2>Manage Parking Slots</h2>
        <div>
            <a href="admin_dashboard.php" class="admin-btn">Dashboard</a>
            <a href="../logout.php" class="admin-btn admin-logout">Logout</a>
        </div>
    </div>

    <div class="admin-card">
        <?php if(isset($msg)): ?>
            <p class="msg" style="color:#16a34a; margin-bottom:18px; font-weight:600;"><?php echo $msg; ?></p>
        <?php endif; ?>

        <form method="POST" style="display:flex; gap:12px; flex-wrap:wrap; align-items:center; margin-bottom:20px;">
            <input type="text" name="slot_number" placeholder="Enter slot number (e.g. A5)" required style="padding:10px; border:1px solid #cbd5e1; border-radius:8px; min-width:220px;">
            <button type="submit" name="add_slot" class="admin-btn" style="padding:10px 18px;">Add Slot</button>
        </form>

        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Slot</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $result = mysqli_query($conn, "SELECT * FROM slots ORDER BY id DESC");
                if(mysqli_num_rows($result) > 0) {
                    while($row = mysqli_fetch_assoc($result)) {
                ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo htmlspecialchars($row['slot_number']); ?></td>
                    <td><?php echo ucfirst($row['status']); ?></td>
                    <td>
                        <a href="admin_slots.php?delete=<?php echo $row['id']; ?>" class="cancel-btn" onclick="return confirm('Delete this slot?');">Delete</a>
                    </td>
                </tr>
                <?php
                    }
                } else {
                    echo '<tr><td colspan="4">No slots found.</td></tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<?php include("../includes/footer.php"); ?>
