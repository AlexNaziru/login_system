<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>

<body>
<nav>
    <div class="logo">
        <img src="assets/logo.png">
        <a href="" target="_blank">Naziru Development</a>
    </div>
        <ul>
            <li><a href="/<?php echo $root_directory ?>/index.php">Home</a></li>
            <li><a href="/<?php echo $root_directory ?>/page1.php">Page 1</a></li>
            <li><a href="/<?php echo $root_directory ?>/page2.php">Page 2</a></li>
            <li><a href="/<?php echo $root_directory ?>/page3.php">Page 3</a></li>
            <!-- if we are logged in we will make these two not show and show my content -->
            <?php
                if (logged_in()) {
                    echo "<li><a href='/{$root_directory}/mycontent.php'>{$_SESSION['username']}'s content</a></li>";
                    echo "<li><a href='/{$root_directory}/logout.php'>Logout</a></li>";
                } else {
                    echo "<li><a href='/{$root_directory}/login.php'>Login</a></li>";
                    echo "<li><a href='/{$root_directory}/register.php'>Register</a></li>";
                }
            ?>
        </ul>
    <button id="menuButton" onclick="openMenu()">
        <i class='bx bx-menu'></i>
    </button>
</nav>

</body>
</html>
