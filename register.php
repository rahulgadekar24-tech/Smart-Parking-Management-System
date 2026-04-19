<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include("db/config.php");

if(isset($_POST['submit'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "INSERT INTO users (name,email,password) VALUES ('$name','$email','$password')";
    
    if($conn->query($sql) === TRUE) {
        $success = "Registered successfully!";
    } else {
        $error = "Error: " . $conn->error;
    }
}
?>

<?php include("includes/header.php"); ?>

<div class="card" style="margin:auto; margin-top:60px;">
    <h2>Register</h2>

    <?php if(isset($success)) { echo "<p style='color:green;'>$success</p>"; } ?>
    <?php if(isset($error)) { echo "<p style='color:red;'>$error</p>"; } ?>

    <form method="POST">
        <input type="text" name="name" placeholder="Enter Name" required><br>
        <input type="email" name="email" placeholder="Enter Email" required><br>
        <input type="password" name="password" placeholder="Enter Password" required><br>
        <button name="submit">Register</button>
    </form>
</div>

<?php include("includes/footer.php"); ?>