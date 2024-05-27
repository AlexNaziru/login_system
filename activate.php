<?php include "includes/init.php" ?>
<?php
// When a user is activated
if (isset($_GET['user'])) {
    $user = $_GET['user'];
    if (isset($_GET['code'])) {
        $code = $_GET['code'];
        $db_code = validation_code($user, $pdo);
        if ($code == $db_code) {
            try {
                $stmnt = $pdo->prepare("UPDATE users SET active = 1 WHERE username = :username");
                $stmnt->execute([':username'=> $user]);
                set_msg("User Activated, Please log in", "success");
                redirect('index.php');
            } catch (PDOException $e) {
                echo "Error: {$e}";
            }
            $_SESSION['message'] = "Codes match, activating user";
            redirect('index.php');
        } else {
            $_SESSION['message'] = "Validation code is not a match - {$db_code}";
            redirect('index.php');
        }
    } else {
        set_msg("No validation code included with activation request");
        redirect('index.php');
    }
} else {
    set_msg("User is not activated!");
    redirect('index.php');
}