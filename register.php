<?php include "includes/init.php"?>
<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fname = $_POST['firstname'];
    $lname = $_POST['lastname'];
    $uname = $_POST['username'];
    $pass = $_POST['password'];
    $pass_conf = $_POST['password_confirm'];
    $email = $_POST['email'];
    $email_conf = $_POST['email_confirm'];
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
    try {
        $stmnt = $pdo->prepare("SELECT username FROM users WHERE username = :username");
        $user_data = [":username"=> $uname];
        $stmnt->execute($user_data);
        if ($stmnt->rowCount() > 0) {
            $error[] = "Username '{$uname}' already exists.";
        }
    } catch (PDOException $exception) {
        echo "Error: {$exception->getMessage()}";
    }

    // Querying for the email
    try {
        $stmnt = $pdo->prepare("SELECT email FROM users WHERE email = :email");
        $user_data = [":email" => $email];
        $stmnt->execute($user_data);
        if ($stmnt->rowCount() > 0) {
            $error[] = "Email '{$email}' already exists";
        }
    } catch (PDOException $exception) {
        echo "Error: {$exception->getMessage()}";
    }

    // Negating isset in case there are no errors
    if (!isset($error)) {
        try { /* This statemant will submit to the database */
            $sql = "INSERT INTO users (firstname, lastname, username, password, validationcode, email, comments, joined, last_login) 
                    /* Prepared statemants */    
                    VALUES (:firstname, :lastname, :username, :password, 'test', :email, :comments, current_date, current_date)";
            $stmnt = $pdo->prepare($sql);
            $user_data = [':firstname' => $fname, ':lastname' => $lname, ':username' => $uname,
                ':password' => $pass, ':email' => $email, ':comments' => $comments];
            $stmnt->execute($user_data);
            echo "Successfully submitted";
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
                                            <input type="email" name="email" id="register_email" tabindex="4" class="form-control" placeholder="Email Address"
                                                   value="<?php echo $email ?>" required >
                                        </div>
                                        <div class="form-group">
                                            <input type="email" name="email_confirm" id="confirm_email" tabindex="4" class="form-control" placeholder="Confirm Email Address"
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