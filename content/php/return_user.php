<?php
include "../../includes/init.php"; // Going back 2 directories

if (logged_in()) {
    $username = $_SESSION["username"];
    $user_data = return_field_data($pdo, "users", "username", $username);
    echo json_encode($user_data);
} else {
    echo "ERROR: No user logged in!";
}