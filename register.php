<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include("db/config.php");

// If already logged in, redirect to respective dashboard
if(isset($_SESSION['role'])) {
    if($_SESSION['role'] === 'admin') {
        header("Location: admin/admin_dashboard.php");
        exit();
    } else {
        header("Location: dashboard.php");
        exit();
    }
}

if(isset($_POST['submit'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "INSERT INTO users (name,email,password) VALUES ('$name','$email','$password')";
    
    if($conn->query($sql) === TRUE) {
        $success = "Registered successfully! You can now sign in.";
    } else {
        $error = "Error: " . $conn->error;
    }
}
?>

<?php include("includes/header.php"); ?>

<div style="max-width: 440px; margin: 4rem auto;">
    <div class="card">
        <div style="text-align:center; margin-bottom: 2rem;">
            <h2>Create Account</h2>
            <p style="color: var(--ink-mute); font-size:0.9rem;">Join Smart Parking today</p>
        </div>

        <?php if(isset($success)) { echo "<div class='alert alert-success'>$success</div>"; } ?>
        <?php if(isset($error)) { echo "<div class='alert alert-error'>$error</div>"; } ?>

        <form method="POST" style="display:flex; flex-direction:column; gap:1rem;">
            <div style="display:flex; flex-direction:column; gap:0.4rem;">
                <label for="name" style="font-size:0.85rem; font-weight:600; color:var(--ink-soft);">Full Name</label>
                <div class="input-wrap">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                    <input type="text" name="name" id="name" placeholder="John Doe" required>
                </div>
            </div>
            
            <div style="display:flex; flex-direction:column; gap:0.4rem;">
                <label for="email" style="font-size:0.85rem; font-weight:600; color:var(--ink-soft);">Email Address</label>
                <div class="input-wrap">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                        <polyline points="22,6 12,13 2,6"></polyline>
                    </svg>
                    <input type="email" name="email" id="email" placeholder="john@example.com" required>
                </div>
            </div>
            
            <div style="display:flex; flex-direction:column; gap:0.4rem;">
                <label for="password" style="font-size:0.85rem; font-weight:600; color:var(--ink-soft);">Password</label>
                <div class="input-wrap">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="11" width="18" height="11" rx="2"></rect>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                    </svg>
                    <input type="password" name="password" id="password" placeholder="Create a password" required>
                    <button type="button" class="toggle-pw" onclick="togglePassword()" aria-label="Toggle password visibility">
                        <svg id="eye-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                    </button>
                </div>
            </div>
            
            <button name="submit" class="btn btn-primary" style="width: 100%; margin-top:0.5rem; padding: 0.85rem;">Create Account</button>
        </form>

        <div style="text-align:center; margin-top:2rem; font-size:0.85rem; color:var(--ink-mute);">
            Already have an account? <a href="login.php" style="color:var(--accent); text-decoration:none; font-weight:600;">Sign in</a>
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

<?php include("includes/footer.php"); ?>