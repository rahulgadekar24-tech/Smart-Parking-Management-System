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

// ❌ Cancel Booking + FREE SLOT
if(isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];

    // get slot id
    $res = mysqli_query($conn, "SELECT slot_id FROM bookings WHERE id=$id");
    if($data = mysqli_fetch_assoc($res)) {
        $slot_id = $data['slot_id'];

        // delete booking
        mysqli_query($conn, "DELETE FROM bookings WHERE id=$id");

        // free slot
        mysqli_query($conn, "UPDATE slots SET status='available' WHERE id=$slot_id");

        header("Location: admin_dashboard.php");
        exit();
    }
}

// 📊 STATS
$total_slots = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM slots"))['total'];
$available_slots = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM slots WHERE status='available'"))['total'];
$booked_slots = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM slots WHERE status='occupied'"))['total'];

// 📈 ANALYTICS
$occupancy_rate = ($total_slots > 0) ? round(($booked_slots / $total_slots) * 100) : 0;

// Booking Trends (last 7 days of bookings, simplified)
$trends_query = mysqli_query($conn, "SELECT DATE(booking_time) as bdate, COUNT(*) as count FROM bookings GROUP BY bdate ORDER BY bdate DESC LIMIT 7");
$dates = [];
$counts = [];
while($row = mysqli_fetch_assoc($trends_query)) {
    $dates[] = date("M d", strtotime($row['bdate']));
    $counts[] = $row['count'];
}
$dates = array_reverse($dates);
$counts = array_reverse($counts);

// Most Booked Slots (All Time)
$popular_query = mysqli_query($conn, "SELECT slots.slot_number, COUNT(*) as count FROM bookings JOIN slots ON bookings.slot_id = slots.id GROUP BY slots.id ORDER BY count DESC LIMIT 5");
$slot_names = [];
$slot_counts = [];
while($row = mysqli_fetch_assoc($popular_query)) {
    $slot_names[] = $row['slot_number'];
    $slot_counts[] = $row['count'];
}

?>

<?php include("../includes/header.php"); ?>

<!-- Include Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="dashboard">

    <!-- TOP BAR -->
    <div class="admin-topbar">
        <h2>Admin Dashboard</h2>

        <div class="topbar-actions">
            <a href="admin_slots.php" class="btn btn-secondary">Manage Slots</a>
            <a href="../logout.php" class="btn btn-danger">Logout</a>
        </div>
    </div>

    <!-- 📊 STATS CARDS -->
    <div class="stats-container">

        <div class="stat-card total">
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

    <!-- 📈 ANALYTICS SECTION -->
    <div class="table-container" style="margin-bottom: 2.5rem;">
        <h3 class="table-title">System Analytics</h3>
        
        <div style="padding: 0.5rem 0 1rem;">
            <div style="display:flex; justify-content:space-between; margin-bottom:0.75rem;">
                <span style="font-weight:600; font-size:0.95rem; color:var(--ink);">Parking Occupancy</span>
                <span style="font-weight:700; color:var(--accent);"><?php echo $occupancy_rate; ?>%</span>
            </div>
            
            <div style="width:100%; background:var(--surface-alt); height:16px; border-radius:100px; overflow:hidden; box-shadow:inset 0 1px 3px rgba(0,0,0,0.05);">
                <div style="height:100%; background:var(--success); width:<?php echo $occupancy_rate; ?>%; transition:width 1s ease-in-out; border-radius:100px;"></div>
            </div>
            
            <p style="color:var(--ink-mute); font-size:0.85rem; margin-top:1rem; display:flex; align-items:center; gap:6px;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="12"></line>
                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                </svg>
                <?php echo $booked_slots; ?> out of <?php echo $total_slots; ?> slots are currently occupied. 
                <?php if($occupancy_rate >= 90): ?>
                    <span style="color:var(--danger); font-weight:600;">(High Traffic)</span>
                <?php endif; ?>
            </p>
        </div>

        <div style="display:flex; flex-wrap:wrap; gap:2rem; margin-top:2rem;">
            <!-- Line Chart (Trends) -->
            <div style="flex:1; min-width:300px; background:var(--surface); border:1px solid var(--border); border-radius:var(--radius-sm); padding:1rem;">
                <h4 style="font-family:'Outfit', sans-serif; color:var(--ink-soft); margin-bottom:1rem; font-size:1rem;">Booking Trends (Recent)</h4>
                <canvas id="trendsChart"></canvas>
            </div>

            <!-- Bar Chart (Popular Slots) -->
            <div style="flex:1; min-width:300px; background:var(--surface); border:1px solid var(--border); border-radius:var(--radius-sm); padding:1rem;">
                <h4 style="font-family:'Outfit', sans-serif; color:var(--ink-soft); margin-bottom:1rem; font-size:1rem;">Most Popular Slots</h4>
                <canvas id="popularChart"></canvas>
            </div>
        </div>
    </div>

    <!-- BOOKINGS TABLE -->
    <div class="table-container">

        <h3 class="table-title">Recent Bookings</h3>

        <table>
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
                    ORDER BY bookings.booking_time DESC
                ");

                if($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                ?>
                <tr>
                    <td><strong><?php echo htmlspecialchars($row['slot_number']); ?></strong></td>
                    <td><?php echo htmlspecialchars($row['user_id']); ?></td>
                    <td><?php echo date("d M Y, h:i A", strtotime($row['booking_time'])); ?></td>
                    <td>
                        <a href="admin_dashboard.php?delete=<?php echo $row['id']; ?>" 
                           class="btn btn-danger"
                           style="padding: 0.4rem 0.8rem; font-size:0.8rem;"
                           onclick="return confirm('Cancel this booking?')">
                           Cancel
                        </a>
                    </td>
                </tr>
                <?php 
                    }
                } else {
                    echo "<tr><td colspan='4' class='text-center' style='color:var(--ink-mute);'>No bookings found</td></tr>";
                }
                ?>
            </tbody>
        </table>

    </div>

</div>

<!-- Chart Rendering Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Shared styling for charts to look good in dark mode too
    const textColor = getComputedStyle(document.documentElement).getPropertyValue('--ink-mute').trim();
    const gridColor = getComputedStyle(document.documentElement).getPropertyValue('--border').trim();
    const accentColor = getComputedStyle(document.documentElement).getPropertyValue('--accent').trim();
    const goldColor = '#c9963c';

    Chart.defaults.color = textColor;
    Chart.defaults.font.family = "'Plus Jakarta Sans', sans-serif";

    // 1. Trends Chart
    const trendsCtx = document.getElementById('trendsChart').getContext('2d');
    new Chart(trendsCtx, {
        type: 'line',
        data: {
            labels: <?php echo json_encode($dates); ?>,
            datasets: [{
                label: 'Bookings',
                data: <?php echo json_encode($counts); ?>,
                borderColor: accentColor,
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, grid: { color: gridColor }, ticks: { stepSize: 1 } },
                x: { grid: { display: false } }
            }
        }
    });

    // 2. Popular Slots Chart
    const popCtx = document.getElementById('popularChart').getContext('2d');
    new Chart(popCtx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($slot_names); ?>,
            datasets: [{
                label: 'Total Bookings',
                data: <?php echo json_encode($slot_counts); ?>,
                backgroundColor: goldColor,
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, grid: { color: gridColor }, ticks: { stepSize: 1 } },
                x: { grid: { display: false } }
            }
        }
    });
});
</script>

<?php include("../includes/footer.php"); ?>