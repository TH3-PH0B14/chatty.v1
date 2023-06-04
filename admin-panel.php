<?php
session_start();

// Redirect to login page if not logged in as admin
if (!isset($_SESSION['admin'])) {
    header("Location: admin-login.php");
    exit();
}

// Logout functionality
if (isset($_GET['logout'])) {
    unset($_SESSION['admin']);
    header("Location: admin-login.php");
    exit();
}

// Ban a user
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['ban_button'])) {
        $username = $_POST['username'];
        $bannedUsers = file('banned_users.txt', FILE_IGNORE_NEW_LINES);

        // Check if the user is already banned
        if (in_array($username, $bannedUsers)) {
            echo "User '$username' is already banned.";
        } else {
            $bannedUsers[] = $username;
            file_put_contents('banned_users.txt', implode(PHP_EOL, $bannedUsers) . PHP_EOL, FILE_APPEND);
            file_put_contents('chatlog.html', "<div><em>Admin: User '$username' has been banned.</em></div>", FILE_APPEND);
            echo "User '$username' has been banned.";
        }
    } elseif (isset($_POST['unban_button'])) {
        $username = $_POST['username'];
        $bannedUsers = file('banned_users.txt', FILE_IGNORE_NEW_LINES);

        // Check if the user is currently banned
        if (in_array($username, $bannedUsers)) {
            // Remove the user from the banned users list
            $bannedUsers = array_diff($bannedUsers, [$username]);
            file_put_contents('banned_users.txt', implode(PHP_EOL, $bannedUsers));
            file_put_contents('chatlog.html', "<div><em>Admin: User '$username' has been unbanned.</em></div>", FILE_APPEND);
            echo "User '$username' has been unbanned.";
        } else {
            echo "User '$username' is not currently banned.";
        }
    } elseif (isset($_POST['add_filter_button'])) {
        $filter_word = $_POST['filter_word'];
        $bannedWords = file('banned_words.txt', FILE_IGNORE_NEW_LINES);

        // Check if the word is already banned
        if (in_array($filter_word, $bannedWords)) {
            echo "Word '$filter_word' is already banned.";
        } else {
            $bannedWords[] = $filter_word;
            file_put_contents('banned_words.txt', implode(PHP_EOL, $bannedWords) . PHP_EOL);
            echo "Word '$filter_word' has been banned.";
        }
    } elseif (isset($_POST['remove_filter_button'])) {
        $filter_word = $_POST['filter_word'];
        $bannedWords = file('banned_words.txt', FILE_IGNORE_NEW_LINES);

        // Check if the word is currently banned
        if (in_array($filter_word, $bannedWords)) {
            // Remove the word from the banned words list
            $bannedWords = array_diff($bannedWords, [$filter_word]);
            file_put_contents('banned_words.txt', implode(PHP_EOL, $bannedWords));
            echo "Word '$filter_word' has been unbanned.";
        } else {
            echo "Word '$filter_word' is not currently banned.";
        }
    } elseif (isset($_POST['clear_logs_button'])) {
        file_put_contents('chatlog.html', "");
        echo "Chat logs cleared successfully.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Panel</title>
</head>
<body>
    <h2>Admin Panel</h2>
    
    <form method="post" action="">
        <input type="text" name="username" placeholder="Enter username to ban or unban" required>
        <input type="submit" name="ban_button" value="Ban User">
        <input type="submit" name="unban_button" value="Unban User">
    </form>

    <form method="post" action="">
        <input type="text" name="filter_word" placeholder="Enter word to ban or unban" required>
        <input type="submit" name="add_filter_button" value="Ban Word">
        <input type="submit" name="remove_filter_button" value="Unban Word">
    </form>

    <form method="post" action="">
        <input type="submit" name="clear_logs_button" value="Clear Chat Logs">
    </form>

    <br>
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
    
    <br>
    <a href="?logout">Logout</a>
</body>
</html>
