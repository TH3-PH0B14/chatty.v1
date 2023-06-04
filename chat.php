<?php
session_start();

// Redirect to login page if not logged in
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

// Logout functionality
if (isset($_GET['logout'])) {
    $username = $_SESSION['username'];
    unset($_SESSION['username']);
    $loggedUsers = file('logged_users.txt', FILE_IGNORE_NEW_LINES);
    $loggedUsers = array_diff($loggedUsers, [$username]);
    file_put_contents('logged_users.txt', implode(PHP_EOL, $loggedUsers));
    file_put_contents('chatlog.html', "<div><em>$username has left the chat.</em></div>", FILE_APPEND);
    header("Location: index.php");
    exit();
}

// Add message to chat log
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = $_POST['message'];
    $username = $_SESSION['username'];
    $timestamp = date('Y-m-d H:i:s');
    
    // Check if the sender is banned
    $bannedUsers = file('banned_users.txt', FILE_IGNORE_NEW_LINES);
    if (in_array($username, $bannedUsers)) {
        header("Location: chat.php");
        exit();
    }
    
    // Check if the message is a link or an image URL
    if (filter_var($message, FILTER_VALIDATE_URL)) {
        $parsedUrl = parse_url($message);
        $extension = pathinfo($parsedUrl['path'], PATHINFO_EXTENSION);
        
        if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) {
            $message = "<img src=\"$message\">";
        } else {
            $message = "<a href=\"$message\" target=\"_blank\">$message</a>";
        }
    }
    
    file_put_contents('chatlog.html', "<div class=msg><strong>$username:</strong> $message <span style='font-size: 0.8em;'>[$timestamp]</span></div>", FILE_APPEND);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Simple Chat Room</title>
    <script>
        // Automatically scroll to the bottom of the chat log
        function scrollToBottom() {
            var chatlog = document.getElementById("chatlog");
            chatlog.scrollTop = chatlog.scrollHeight;
        }
    </script>
    <link rel="stylesheet" href="home.css">
</head>
<body onload="scrollToBottom()">
        <div class="logo">
<pre>
             __                   
 _    _____ / /______  __ _  ___  
| |/|/ / -_) / __/ _ \/  ' \/ -_) 
|__,__/\__/_/\__/\___/_/_/_/\__(_)
</pre>
    </div>
        <div id="chatlog" >
        <?php
        $chatlog = file_get_contents('chatlog.html');
        $bannedUsers = file('banned_users.txt', FILE_IGNORE_NEW_LINES);
        $bannedWords = file('banned_words.txt', FILE_IGNORE_NEW_LINES);

        // Filter out messages from banned users and replace banned words
        $filteredChatlog = '';
        $messages = explode('<div>', $chatlog);
        foreach ($messages as $message) {
            $username = strip_tags($message);
            $content = $message;
            foreach ($bannedWords as $bannedWord) {
                $content = preg_replace("/\b" . preg_quote($bannedWord, "/") . "\b/i", '(banned)', $content);
            }
            if (!in_array($username, $bannedUsers)) {
                $filteredChatlog .= "<div>$content";
            }
        }
        
        echo $filteredChatlog;
        ?>
    </div>
    </div>
    <br>
    <form method="POST" action="">
        <input type="text" id="message" name="message" placeholder="Enter your message, <?php echo $_SESSION['username']; ?>." required>
        <input type="submit" value="Send">
    </form>
    <br>
    <a class="log" href="?logout">Logout</a>

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