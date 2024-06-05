<?php include "includes/init.php"?>
<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fname = $_POST['firstname'];
    $lname = $_POST['lastname'];
    $uname = $_POST['username'];
    $pass = $_POST['password'];
    $pass_conf = $_POST['confirm-password'];
    $email = $_POST['email'];
    $email_conf = $_POST['confirm_email'];
    $comments = $_POST['comments'];

    // Validation Code
    if (strlen($lname) < 3) {
        // array of error messages
        $error[] = "Lastname must be at least 3 characters long!";
    }
    if (strlen($uname) < 6) {
        $error[] = "Username must be at least 6 characters long!";
    }
    if (strlen($pass) < 8) {
        $error[] = "Password must be at least 8 characters long!";
    }
    if ($pass != $pass_conf) {
        $error[] = "Passwords do not match!";
    }
    if ($email != $email_conf) {
        $error[] = "Email's do not match!";
    }

    // Querying the db to see if there are duplicate usernames
    if (count_field_val($pdo, "users", "username", $uname) != 0) {
        $error[] = "Username '{$uname}' already exists.";
    }
    if (count_field_val($pdo, "users", "email", $email) !=0) {
        $error[] = "Email '{$email}' already exists";
    }

    // Negating isset in case there are no errors
    if (!isset($error)) {
        $vcode = generate_token();
        try { /* This statemant will submit to the database */
            $sql = "INSERT INTO users (firstname, lastname, username, password, validationcode, email, comments, joined, last_login) 
                    /* Prepared statemants */    
                    VALUES (:firstname, :lastname, :username, :password, :vcode, :email, :comments, current_date, current_date)";
            $stmnt = $pdo->prepare($sql);
            $user_data = [':firstname' => $fname, ':lastname' => $lname, ':username' => $uname,
                ':password' => password_hash($pass, PASSWORD_BCRYPT), ':email' => $email, ':comments' => $comments,
                ':vcode'=>$vcode];
            $stmnt->execute($user_data);

            // Sending verification e-mail
            $body = "Click on the link bellow to activate your account!\r\n
                      http://{$_SERVER['SERVER_NAME']}/{$root_directory}/activate.php?user={$uname}&code={$vcode}'>Activate account";
            send_email($email, "Activate User", $body, $from_email, $reply_email);
            // Redirecting user after a successful registration
            $_SESSION['message'] = "User successfully registered";
            redirect("index.php");
        } catch (PDOException $exception) {
            echo "Error: " . $exception->getMessage();
        }
    }
        } else {
    // In order so our data in the register form won't disappear when we type something wrong, we have to pop the variables with empty ""
        $fname = "";
        $lname = "";
        $uname = "";
        $email = "";
        $email_conf = "";
        $comments = "";
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
                    if (isset($error)) {
                        foreach ($error as $msg) {
                            echo "<p class='bg-danger text-center'>{$msg}</p>";
                        }
                    }
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
                                            <input type="text" name="firstname" id="firstname" tabindex="1" class="form-control" placeholder="First Name"
                                                   value="<?php echo $fname ?>" required >
                                        </div>
                                        <div class="form-group">
                                            <input type="text" name="lastname" id="lastname" tabindex="2" class="form-control" placeholder="Last Name"
                                                   value="<?php echo $lname ?>" required >
                                        </div>
                                        <div class="form-group">
                                            <input type="text" name="username" id="username" tabindex="3" class="form-control" placeholder="Username"
                                                   value="<?php echo $uname ?>" required >
                                        </div>
                                        <div class="form-group">
                                            <input type="email" name="email" id="email" tabindex="4" class="form-control" placeholder="Email Address"
                                                   value="<?php echo $email ?>" required >
                                        </div>
                                        <div class="form-group">
                                            <input type="email" name="confirm_email" id="confirm_email" tabindex="4" class="form-control" placeholder="Confirm Email Address"
                                                   value="<?php echo $email_conf ?>" required >
                                        </div>
                                        <div class="form-group">
                                            <input type="password" name="password" id="password" tabindex="5" class="form-control" placeholder="Password" required>
                                        </div>
                                        <div class="form-group">
                                            <input type="password" name="password_confirm" id="confirm-password" tabindex="6" class="form-control" placeholder="Confirm Password" required>
                                        </div>
                                        <div class="form-group">
                                            <textarea name="comments" id="comments" tabindex="7" class="form-control" placeholder="Comments">
                                                <?php echo $comments ?>
                                            </textarea>
                                        </div>
                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-sm-6 col-sm-offset-3">
                                                    <input type="submit" name="register-submit" id="register-submit" tabindex="4" class="form-control btn btn-custom" value="Register Now">
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