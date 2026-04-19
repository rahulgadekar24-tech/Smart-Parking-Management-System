<?php
session_start();
include("../db/config.php");

// 🔐 Restrict access
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: admin_login.php");
    exit();
}

// Prevent caching to ensure logout works securely on 'back' button
header("Cache-Control: no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");

// ➕ Add Slot
if(isset($_POST['add_slot'])) {
    $slot_number = mysqli_real_escape_string($conn, trim($_POST['slot_number']));
    $query = "INSERT INTO slots (slot_number, status) VALUES ('$slot_number','available')";
    if(mysqli_query($conn, $query)) {
        $msg = "Slot added successfully.";
    } else {
        $error = "Error adding slot. It may already exist.";
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
        <div class="topbar-actions">
            <a href="admin_dashboard.php" class="btn btn-secondary">Dashboard</a>
            <a href="../logout.php" class="btn btn-danger">Logout</a>
        </div>
    </div>

    <div class="table-container">
        <h3 class="table-title">Add New Slot</h3>
        
        <?php if(isset($msg)): ?>
            <div class="alert alert-success"><?php echo $msg; ?></div>
        <?php endif; ?>
        <?php if(isset($error)): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" style="display:flex; gap:12px; flex-wrap:wrap; align-items:center; margin-bottom:3rem;">
            <input type="text" name="slot_number" class="input-field" placeholder="Enter slot number (e.g. A5)" required style="margin-bottom:0; min-width:220px; flex:1; max-width:300px;">
            <button type="submit" name="add_slot" class="btn btn-primary" style="padding:0.75rem 1.2rem;">Add Slot</button>
        </form>

        <h3 class="table-title">All Slots</h3>
        <table>
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
                    <td><strong><?php echo htmlspecialchars($row['slot_number']); ?></strong></td>
                    <td>
                        <?php if($row['status'] == 'available'): ?>
                            <span style="color:var(--success); font-weight:600; background:var(--success-bg); padding:4px 8px; border-radius:100px; font-size:0.8rem;">Available</span>
                        <?php else: ?>
                            <span style="color:var(--accent); font-weight:600; background:var(--accent-light); padding:4px 8px; border-radius:100px; font-size:0.8rem;">Occupied</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="admin_slots.php?delete=<?php echo $row['id']; ?>" class="btn btn-danger" style="padding:0.4rem 0.8rem; font-size:0.8rem;" onclick="return confirm('Delete this slot?');">Delete</a>
                    </td>
                </tr>
                <?php
                    }
                } else {
                    echo '<tr><td colspan="4" class="text-center" style="color:var(--ink-mute);">No slots found.</td></tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<?php include("../includes/footer.php"); ?>
