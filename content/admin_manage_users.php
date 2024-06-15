<?php
include("../includes/init.php");?>

<!-- Taking info from the form and adding it to the database -->
<?php

// We want to check if we submitted any POST data is getting the group id with the GET request
if (isset($_GET['id'])) {
    $group_id = $_GET['id'];
} else {
    redirect("admin.php");
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_POST['user_id'];
    try {
        $stmnt = $pdo->prepare("INSERT INTO user_group_link (user_id, group_id) VALUES (:user_id, :group_id)");
        $stmnt->execute([":user_id"=>$user_id, ":group_id"=>$group_id]);
        set_msg("User '{$user_id}' has been added  to {$group_id}", "success");
        redirect("admin.php?tab=groups ");
    } catch (PDOException $exception) {
        echo "Error: ".$exception->getMessage();
    }
} ?>

<!DOCTYPE html>
<html lang="en">
<?php include "../includes/header.php" ?>
<body>
<?php include "../includes/nav.php" ?>

<div class="container">
    <?php show_msg(); ?>
    <div class="row">
        <div class="col-lg-6 col-lg-offset-3">
            <?php if (isset($error)) {
                foreach ($error as $msg) {
                    echo "<h4 class='bg-danger text-center'>{$msg}</h4>";
                }
            } ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 col-md-offset-3">
            <div class="panel panel-login">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-lg-12">
                            <form id="register-form" method="post" role="form" >
                                <div class="form-group">
                                    <select name="user_id" id="user_id" tabindex="8" class="form-control">
                                        <?php
                                        try {
                                            /* Querying the database */
                                            $result = $pdo->query("SELECT id, username FROM users ORDER BY username");
                                            // Looping threw the results
                                            foreach ($result as $row) {
                                                echo "<option value={$row['id']}> {$row['username']}</option>";
                                            }
                                        } catch (PDOException $exception) {
                                            echo "Error: ".$exception->getMessage();
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-sm-6 col-sm-offset-3">
                                            <input type="submit" name="manage-submit" id="manage-submit" tabindex="4" class="form-control btn btn-custom" value="Add User to group">
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include "../includes/footer.php" ?>
</body>
</html>
