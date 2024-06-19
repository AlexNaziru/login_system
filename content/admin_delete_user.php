<?php include ("../includes/init.php"); ?>

<?php
if (logged_in()) {
    $username = $_SESSION['username'];
    if (verify_user_group($pdo, $username, "Admin")) {
        set_msg("User '{$username}' does not have permission to view this page");
        redirect("../index.php");
    }
} else {
    set_msg("Please log in and try again");
    redirect("../index.php");
}
?>

<?php
if (isset($_GET['id'])) {
    $user_id = $_GET['id'];
    try {
        $stmnt = $pdo->prepare("DELETE FROM users WHERE id = :id");
        $stmnt->execute([':id'=>$user_id]);
        // Cascade deleting
        $stmnt = $pdo->prepare("DELETE FROM user_group_link WHERE user_id = :id");
        $stmnt->execute([':id'=>$user_id]);
        redirect("admin.php");
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
} else {
    redirect("admin.php");
}
?>