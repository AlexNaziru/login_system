<?php
include "includes/init.php";
?>

<?php
// Checking if the submit btn was pressed
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    // checking if the username is in our db
    if (count_field_val($pdo, "users", "username", $username) > 0) {
        // if the users exists will reset the password
        $row = return_field_data($pdo, "users", "username", $username);

        // now we will send an e-mail
        $body = "Click on the link bellow to activate your account!\r\n
                      http://{$_SERVER['SERVER_NAME']}/{$root_directory}/reset_2.php?user={$username}&code={$row['validationcode']}'>Reset your password";
        send_email($row['email'], "Reset password", $body, $from_email, $reply_email);
    } else {
        set_msg("User '{$username} was not found!'");
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<?php include "includes/header.php" ?>
<body>
<?php include "includes/nav.php" ?>
<div class="container">
    <div class="row">
        <div class="col-md-6 col-md-offset-3">
            <?php
            show_msg();
            ?>
            <div class="panel panel-login">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-lg-12">
                            <h3 class="text-center">Reset password</h3>
                            <form id="login-form"  method="post" role="form" style="display: block;">
                                <div class="form-group">
                                    <input type="text" name="username" id="username" tabindex="1" class="form-control"
                                           placeholder="Username" required>
                                </div>

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-sm-6 col-sm-offset-3">
                                            <input type="submit" name="reset-password" id="reset-password" tabindex="4" class="form-control btn btn-custom" value="Reset password">
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
