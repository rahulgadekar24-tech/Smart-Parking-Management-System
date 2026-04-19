<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// If already logged in as admin, redirect to dashboard
if(isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    header("Location: admin_dashboard.php");
    exit();
}

if(isset($_POST['login'])) {

    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // 🔐 Fixed Admin Credentials
    if($username === "admin" && $password === "admin123") {
        $_SESSION['admin'] = $username;
        $_SESSION['role'] = "admin";

        header("Location: admin_dashboard.php");
        exit();
    } else {
        $error = "Invalid Admin Login";
    }
}
?>

<?php include("../includes/header.php"); ?>

<div style="max-width: 440px; margin: 4rem auto;">
    <div class="card">
        <div style="text-align:center; margin-bottom: 2rem;">
            <div style="display:inline-flex; align-items:center; justify-content:center; width:48px; height:48px; background:var(--accent-light); border-radius:var(--radius-sm); margin-bottom:1rem;">
                <svg style="color:var(--accent); width:24px; height:24px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="11" width="18" height="11" rx="2"></rect>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                </svg>
            </div>
            <h2>Admin Login</h2>
            <p style="color: var(--ink-mute); font-size:0.9rem;">Sign in to manage the system</p>
        </div>

        <?php if(isset($error)) { echo "<div class='alert alert-error'>$error</div>"; } ?>

        <form method="POST" style="display:flex; flex-direction:column; gap:1rem;">
            <div style="display:flex; flex-direction:column; gap:0.4rem;">
                <label for="username" style="font-size:0.85rem; font-weight:600; color:var(--ink-soft);">Username</label>
                <div class="input-wrap">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                    <input type="text" name="username" id="username" placeholder="Enter admin username" required>
                </div>
            </div>
            
            <div style="display:flex; flex-direction:column; gap:0.4rem;">
                <label for="password" style="font-size:0.85rem; font-weight:600; color:var(--ink-soft);">Password</label>
                <div class="input-wrap">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="11" width="18" height="11" rx="2"></rect>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                    </svg>
                    <input type="password" name="password" id="password" placeholder="Enter password" required>
                    <button type="button" class="toggle-pw" onclick="togglePassword()" aria-label="Toggle password visibility">
                        <svg id="eye-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                    </button>
                </div>
            </div>
            
            <button name="login" class="btn btn-primary" style="width: 100%; margin-top:0.5rem; padding: 0.85rem;">Login as Admin</button>
        </form>

        <div style="text-align:center; margin-top:2rem; font-size:0.85rem; color:var(--ink-mute);">
            <a href="../index.php" style="color:var(--ink-soft); text-decoration:none; font-weight:500;">
                ← Back to Home
            </a>
        </div>
    </div>
</div>

<script>
  function togglePassword() {
    const input = document.getElementById('password');
    const icon  = document.getElementById('eye-icon');
    const show  = input.type === 'password';
    input.type  = show ? 'text' : 'password';
    icon.innerHTML = show
      ? '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/>'
      : '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>';
  }
</script>

<?php include("../includes/footer.php"); ?>