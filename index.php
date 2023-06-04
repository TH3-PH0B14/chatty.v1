<?php
session_start();

// Check if user is already logged in
if (isset($_SESSION['username'])) {
    header("Location: chat.php");
    exit();
}

// Check login credentials
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'];

    // Check if password is correct
    if ($password === '123test') {
        $username = $_POST['username'];

        // Check if username is already in use
        $loggedUsers = file('logged_users.txt', FILE_IGNORE_NEW_LINES);
        if (in_array($username, $loggedUsers)) {
            $error = "Username is already in use.";
        } else {
            // Set username in session and redirect to chat page
            $_SESSION['username'] = $username;
            file_put_contents('logged_users.txt', $username . PHP_EOL, FILE_APPEND);
            header("Location: chat.php");
            exit();
        }
    } else {
        $error = "Invalid password.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chatty - Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="center">
        <div class="logo">
    <pre>
  _______        __  __            ___
 / ___/ /  ___ _/ /_/ /___ __ _  _<  /
/ /__/ _ \/ _ `/ __/ __/ // /| |/ / / 
\___/_//_/\_,_/\__/\__/\_, (_)___/_/  
                      /___/           
    </pre>
        </div>

        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
        <form class="frm" method="POST" action="">
            <input placeholder="Password" autocomplete="off" type="password" id="password" name="password" required><br><br>
            <input placeholder="Username" autocomplete="off" type="text" id="username" name="username" required><br><br>
            <input id="sub" autocomplete="off" type="submit" value="Login">
        </form>
        <a href="admin-login.php">Admin Login</a>
    </div>
        
<script>

    // List of available background colors
    const colors = [
    "#897a6b",
    "#73896b",
    "#896c6b",
    "#6b6e89",
    "#886b89"
    ];

    // Randomly select a color from the array
    const randomColor = colors[Math.floor(Math.random() * colors.length)];

    // Set the selected color as the body's background color
    document.body.style.backgroundColor = randomColor;

</script>
</body>
</html>