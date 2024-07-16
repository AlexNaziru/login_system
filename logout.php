<?php
// For access to our session variable
include "includes/init.php";

unset($_SESSION['username']);

if (isset($_COOKIE['username'])) {
    // key, value and expiration date to destroy the cookie
    setcookie('username', 'delete', time() - 3600);
}
redirect('index.php');

