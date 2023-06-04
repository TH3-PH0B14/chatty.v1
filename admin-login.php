<?php
session_start();

// Check if the admin is already logged in
if (isset($_SESSION['admin'])) {
    header("Location: admin-panel.php");
    exit();
}

// Admin login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $adminUsername = "admin"; // Replace with your desired admin username
    $adminPassword = "admin123"; // Replace with your desired admin password

    $inputUsername = $_POST['username'];
    $inputPassword = $_POST['password'];

    // Check if the entered username and password match the admin credentials
    if ($inputUsername === $adminUsername && $inputPassword === $adminPassword) {
        $_SESSION['admin'] = $adminUsername;
        header("Location: admin-panel.php");
        exit();
    } else {
        $errorMessage = "Invalid username or password.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Login</title>
</head>
<body>
    <h2>Admin Login</h2>

    <?php if (isset($errorMessage)): ?>
        <p style="color: red;"><?php echo $errorMessage; ?></p>
    <?php endif; ?>

    <form method="POST" action="">
        <input type="text" name="username" placeholder="Username" required>
        <br><br>
        <input type="password" name="password" placeholder="Password" required>
        <br><br>
        <input type="submit" value="Login">
    </form>
</body>
</html>
