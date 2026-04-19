<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include("db/config.php");

if(isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // 🔐 Fixed Admin Credentials
    $admin_email = "admin@gmail.com";
    $admin_password = "admin123";

    // ✅ Admin Login Check
    if($email == $admin_email && $password == $admin_password) {
        $_SESSION['user'] = "Admin";
        $_SESSION['user_email'] = $admin_email;
        $_SESSION['user_id'] = 0;
        $_SESSION['role'] = "admin";

        header("Location: admin/admin_dashboard.php");
        exit();
    }

    // 👤 Normal User Login (from database)
    $result = $conn->query("SELECT * FROM users WHERE email='$email' AND password='$password'");

    if($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        $_SESSION['user'] = $row['name'];
        $_SESSION['user_email'] = $row['email'];
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['role'] = "user";

        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Invalid login";
    }
}
?>

<?php include("includes/header.php"); ?>

<div class="card" style="margin:auto; margin-top:50px;">
    <h2>Login</h2>

    <?php if(isset($error)) { echo "<p style='color:red;'>$error</p>"; } ?>

    <form method="POST">
        <input type="email" name="email" placeholder="Enter Email" required><br><br>
        <input type="password" name="password" placeholder="Enter Password" required><br><br>
        <button name="login">Login</button>
    </form>
</div>

<?php include("includes/footer.php"); ?>