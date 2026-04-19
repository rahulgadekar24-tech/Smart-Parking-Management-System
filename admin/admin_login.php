<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

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

<div class="card" style="margin:auto; margin-top:60px;">
    <h2>Admin Login</h2>

    <?php if(isset($error)) { echo "<p style='color:red;'>$error</p>"; } ?>

    <form method="POST">
        <input type="text" name="username" placeholder="Enter Admin Username" required><br><br>
        <input type="password" name="password" placeholder="Enter Password" required><br><br>
        <button name="login">Login</button>
    </form>
</div>

<?php include("../includes/footer.php"); ?>