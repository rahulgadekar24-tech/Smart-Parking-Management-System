<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Parking System</title>
    <link rel="stylesheet" type="text/css" href="/smart-parking/css/style.css?v=4">
    <script>
        // Check theme immediately to prevent flash
        const savedTheme = localStorage.getItem('theme');
        if (savedTheme === 'dark' || (!savedTheme && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.setAttribute('data-theme', 'dark');
        }
    </script>
</head>
<body>

<header class="navbar">
    <a href="/smart-parking/index.php" style="text-decoration:none;">
        <h2>🚗 Smart Parking</h2>
    </a>
    <div class="nav-links" style="display:flex; gap:0.5rem; align-items:center;">
        <button class="theme-toggle" id="theme-toggle" aria-label="Toggle theme">
            <svg id="theme-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <!-- Moon icon default -->
                <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
            </svg>
        </button>
        <?php if(isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
            <a href="/smart-parking/admin/admin_dashboard.php" class="btn btn-secondary" style="padding:0.4rem 0.8rem; font-size:0.8rem;">Admin Dashboard</a>
        <?php else: ?>
            <a href="/smart-parking/admin/admin_login.php" class="btn btn-secondary" style="padding:0.4rem 0.8rem; font-size:0.8rem;">Admin Panel</a>
        <?php endif; ?>

        <?php if(isset($_SESSION['user'])): ?>
            <?php if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin'): ?>
                <a href="/smart-parking/dashboard.php" class="btn btn-primary" style="padding:0.4rem 0.8rem; font-size:0.8rem;">My Dashboard</a>
            <?php endif; ?>
            <a href="/smart-parking/logout.php" class="btn btn-danger" style="padding:0.4rem 0.8rem; font-size:0.8rem;">Logout</a>
        <?php else: ?>
            <a href="/smart-parking/login.php" class="btn btn-primary" style="padding:0.4rem 0.8rem; font-size:0.8rem;">Sign In</a>
        <?php endif; ?>
    </div>
</header>

<div class="main-container">