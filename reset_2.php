<?php
include "includes/init.php";
?>
<?php
// Checking to see if we have our user Get parameter
if ($_GET['user']) {
    // Checking to see if there is a validation code with the user
    if ($_GET['code']) {
        $username = $_GET['user'];

        // Checking now to see if that code is valid
        $code = $_GET['code'];
        if (count_field_val($pdo, "users", "username", $username) > 0) {
            $row = return_field_data($pdo, "users", "username", $username);
            if ($code != $row['validationcode']) {
                set_msg("Validation code does not match!");
                redirect("index.php");
            }
        } else {
            set_msg("User '{$username}' not found!");
            redirect("index.php");
        }
    } else {
        set_msg("No validation code included with reset request");
        redirect("index.php");
    }
} else {
    // if we don't have a user parameter
    set_msg("No user with reset request");
    redirect("index.php");
}
?>

<!DOCTYPE html>
<html lang="en">
<?php include "includes/header.php" ?>
<body>
<?php include "includes/nav.php" ?>

<div class="container">
    <div class="row">
        <div class="col-lg-6 col-lg-offset-3">
            <?php
            show_msg();
            ?>
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
                                    <input type="password" name="password" id="password" tabindex="5" class="form-control" placeholder="Password" required>
                                </div>
                                <div class="form-group">
                                    <input type="password" name="password_confirm" id="confirm-password" tabindex="6" class="form-control" placeholder="Confirm Password" required>
                                </div>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-sm-6 col-sm-offset-3">
                                            <input type="submit" name="reset-submit" id="reset-submit" tabindex="4" class="form-control btn btn-custom" value="Reset">
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
<?php include "includes/footer.php" ?>
</body>
</html>
