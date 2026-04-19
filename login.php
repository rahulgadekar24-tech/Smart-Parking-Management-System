<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
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

$error = "";

if(isset($_POST['login'])) {
    $email    = trim($_POST['email']);
    $password = trim($_POST['password']);

    // 🔐 Fixed Admin Credentials
    $admin_email    = "admin@gmail.com";
    $admin_password = "admin123";

    // ✅ Admin Login Check
    if($email === $admin_email && $password === $admin_password) {
        $_SESSION['user']       = "Admin";
        $_SESSION['user_email'] = $admin_email;
        $_SESSION['user_id']    = 0;
        $_SESSION['role']       = "admin";

        header("Location: admin/admin_dashboard.php");
        exit();
    }

    // 👤 Normal User Login (from database)
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? AND password = ?");
    $stmt->bind_param("ss", $email, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        $_SESSION['user']       = $row['name'];
        $_SESSION['user_email'] = $row['email'];
        $_SESSION['user_id']    = $row['id'];
        $_SESSION['role']       = "user";

        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Incorrect email or password. Please try again.";
    }
}
?>
<?php include("includes/header.php"); ?>

<style>
  /* ── PAGE WRAPPER ── */
  .login-page {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2rem 1.25rem;
    position: relative;
    overflow: hidden;
  }

  /* Grid bg */
  .login-page::before {
    content: '';
    position: fixed;
    inset: 0;
    background-image:
      linear-gradient(rgba(26,86,219,0.04) 1px, transparent 1px),
      linear-gradient(90deg, rgba(26,86,219,0.04) 1px, transparent 1px);
    background-size: 40px 40px;
    pointer-events: none;
    z-index: 0;
  }

  /* Glow blob */
  .login-page::after {
    content: '';
    position: fixed;
    top: -25%;
    right: -15%;
    width: 600px;
    height: 600px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(59,111,239,0.08) 0%, transparent 65%);
    pointer-events: none;
    z-index: 0;
  }

  .blob-btm {
    position: fixed;
    bottom: -20%;
    left: -10%;
    width: 500px;
    height: 500px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(26,86,219,0.05) 0%, transparent 65%);
    pointer-events: none;
    z-index: 0;
  }

  /* ── CARD ── */
  .login-card {
    background: var(--surface-card);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 2.5rem 2.25rem;
    width: 100%;
    max-width: 420px;
    position: relative;
    z-index: 1;
    animation: fadeUp 0.55s ease both;
  }

  @keyframes fadeUp {
    from { opacity: 0; transform: translateY(24px); }
    to   { opacity: 1; transform: translateY(0); }
  }

  /* ── CARD HEADER ── */
  .card-header {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    gap: 0.3rem;
    margin-bottom: 2rem;
  }

  .card-icon-wrap {
    width: 44px;
    height: 44px;
    background: var(--accent-light);
    border-radius: var(--radius-sm);
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 0.85rem;
  }

  .card-icon-wrap svg {
    width: 22px;
    height: 22px;
    color: var(--accent);
  }

  .card-title {
    font-family: 'Outfit', sans-serif;
    font-size: 1.55rem;
    font-weight: 700;
    color: var(--ink);
    letter-spacing: -0.02em;
    line-height: 1.2;
  }

  .card-subtitle {
    font-size: 0.85rem;
    color: var(--ink-mute);
    font-weight: 400;
    margin-top: 0.1rem;
  }

  /* ── ERROR ALERT ── */
  .alert-error {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    background: var(--danger-bg);
    border: 1px solid var(--danger-border);
    border-radius: var(--radius-xs);
    padding: 0.75rem 1rem;
    margin-bottom: 1.25rem;
    animation: shake 0.35s ease;
  }

  @keyframes shake {
    0%,100% { transform: translateX(0); }
    20%      { transform: translateX(-5px); }
    40%      { transform: translateX(5px); }
    60%      { transform: translateX(-3px); }
    80%      { transform: translateX(3px); }
  }

  .alert-error svg {
    width: 16px;
    height: 16px;
    color: var(--danger);
    flex-shrink: 0;
    margin-top: 1px;
  }

  .alert-error span {
    font-size: 0.825rem;
    color: var(--danger);
    line-height: 1.5;
  }

  /* ── FORM ── */
  .login-form {
    display: flex;
    flex-direction: column;
    gap: 1rem;
  }

  .field {
    display: flex;
    flex-direction: column;
    gap: 0.4rem;
  }

  .field label {
    font-size: 0.8rem;
    font-weight: 500;
    color: var(--ink-soft);
    letter-spacing: 0.01em;
  }



  /* ── SUBMIT BUTTON ── */
  .btn-login {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    width: 100%;
    padding: 0.75rem 1rem;
    background: var(--accent);
    color: #fff;
    border: none;
    border-radius: var(--radius-xs);
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 0.925rem;
    font-weight: 600;
    cursor: pointer;
    margin-top: 0.5rem;
    transition: background 0.2s, transform 0.15s, box-shadow 0.2s;
  }

  .btn-login:hover {
    background: var(--accent-dark);
    transform: translateY(-1px);
    box-shadow: 0 6px 18px rgba(26,86,219,0.28);
  }

  .btn-login:active {
    transform: translateY(0);
    box-shadow: none;
  }

  .btn-arrow { transition: transform 0.2s ease; }
  .btn-login:hover .btn-arrow { transform: translateX(3px); }

  /* ── DIVIDER ── */
  .divider {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin: 1.5rem 0 1.25rem;
  }

  .divider::before,
  .divider::after {
    content: '';
    flex: 1;
    height: 1px;
    background: var(--border);
  }

  .divider span {
    font-size: 0.75rem;
    color: var(--ink-mute);
    white-space: nowrap;
  }

  /* ── FOOTER LINKS ── */
  .card-footer {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.65rem;
  }

  .register-link {
    font-size: 0.85rem;
    color: var(--ink-soft);
  }

  .register-link a {
    color: var(--accent);
    font-weight: 500;
    text-decoration: none;
    transition: color 0.15s;
  }

  .register-link a:hover { color: var(--accent-dark); text-decoration: underline; }

  .home-link {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 0.8rem;
    color: var(--ink-mute);
    text-decoration: none;
    padding: 0.4rem 0.75rem;
    border-radius: 100px;
    transition: background 0.15s, color 0.15s;
  }

  .home-link:hover {
    background: var(--accent-light);
    color: var(--accent);
  }

  .home-link svg { width: 13px; height: 13px; }

  @media (max-width: 480px) {
    .login-card { padding: 2rem 1.5rem; }
  }
</style>

<div class="login-page">
  <div class="blob-btm"></div>

  <div class="login-card">

    <!-- Header -->
    <div class="card-header">
      <div class="card-icon-wrap">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
          <rect x="3" y="11" width="18" height="11" rx="2"/>
          <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
        </svg>
      </div>
      <h2 class="card-title">Welcome back</h2>
      <p class="card-subtitle">Sign in to your parking account</p>
    </div>

    <!-- Error alert -->
    <?php if($error): ?>
    <div class="alert-error">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <circle cx="12" cy="12" r="10"/>
        <line x1="12" y1="8" x2="12" y2="12"/>
        <line x1="12" y1="16" x2="12.01" y2="16"/>
      </svg>
      <span><?php echo htmlspecialchars($error); ?></span>
    </div>
    <?php endif; ?>

    <!-- Form -->
    <form method="POST" class="login-form" novalidate>

      <div class="field">
        <label for="email">Email address</label>
        <div class="input-wrap">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
            <rect x="2" y="4" width="20" height="16" rx="2"/>
            <path d="m22 7-10 7L2 7"/>
          </svg>
          <input
            type="email"
            id="email"
            name="email"
            placeholder="you@example.com"
            value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
            autocomplete="email"
            required
          >
        </div>
      </div>

      <div class="field">
        <label for="password">Password</label>
        <div class="input-wrap">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
            <rect x="3" y="11" width="18" height="11" rx="2"/>
            <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
          </svg>
          <input
            type="password"
            id="password"
            name="password"
            placeholder="Enter your password"
            autocomplete="current-password"
            required
          >
          <button type="button" class="toggle-pw" onclick="togglePassword()" aria-label="Toggle password visibility">
            <svg id="eye-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
              <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
              <circle cx="12" cy="12" r="3"/>
            </svg>
          </button>
        </div>
      </div>

      <button type="submit" name="login" class="btn-login">
        Sign in <span class="btn-arrow">→</span>
      </button>

    </form>

    <!-- Footer -->
    <div class="divider"><span>or</span></div>

    <div class="card-footer">
      <p class="register-link">Don't have an account? <a href="register.php">Create one</a></p>
      <a href="index.php" class="home-link">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
          <path d="M19 12H5M12 5l-7 7 7 7"/>
        </svg>
        Back to home
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

<?php include("includes/footer.php"); ?>