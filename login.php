<?php
include "includes/init.php";
?>
<?php
// Checking if someone has clicked the submit btn
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    // Remember value
    if (isset($_POST["remember"])) {
        $remember = "on";
    } else {
        $remember = "off";
    }

    // verifying if the user exists
    if (count_field_val($pdo, "users", "username", $username) > 0) {
        $user_data = return_field_data($pdo, "users", "username", $username);

        // Verifying if the user is activated
        if ($user_data["active"] == 1) {
            // Checking if the password matched the db
            if (password_verify($password, $user_data["password"])) {
                set_msg("Logged in successfully", "success");

                // Update last_login date
                update_login_date($pdo, $username);

                // Setting our session variable
                $_SESSION["username"] = $username;

                // If remember is set to on we will set a cookie with all the info to remember the ON value
                if ($remember == "on") {
                    // key, value and a time in unix (here is a day, bc there are 86400 secs/day, if you want for a week, we put *7)
                    setcookie("username", $username, time() + 86400, "/", null, false, true);
                }  # "/" root directory - means the cookie will be accessible from anywhere of our site. The last one is httpOnly to true

                redirect("mycontent.php");
            } else {
                set_msg("Invalid password");
            }
        } else {
            set_msg("User '{$username}' found, but it's not activated!");
        }
    } else {
        set_msg("User '{$username}' does not exist!");
    }
} else {
    $username = "";
    $password = "";
}
?>
<!DOCTYPE html>
<html lang="en">
    <style>
        .container {
            margin-top: 100px;
        }
        .panel-login {
            border-color: #ccc;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .panel-login .panel-heading {
            color: #1c1e53;
            background-color: #f7f7f7;
            border-color: #fff;
            text-align: center;
            padding: 20px 0;
        }
        .panel-login .panel-heading a {
            text-decoration: none;
            color: #666;
            font-weight: bold;
            font-size: 18px;
            transition: all 0.1s linear;
        }
        .panel-login .panel-heading a.active {
            color: #5e3bee;
            font-size: 24px;
        }
        .panel-login .panel-heading hr {
            margin-top: 10px;
            margin-bottom: 0;
            clear: both;
            border: 0;
            height: 1px;
            background: #ddd;
        }
        .panel-login input[type="text"], .panel-login input[type="password"] {
            height: 45px;
            border: 1px solid #ddd;
            font-size: 16px;
        }
        .btn-custom {
            background-color: #5e3bee;
            color: #fff;
            border-color: #5e3bee;
        }
        .btn-custom:hover, .btn-custom:focus {
            background-color: #5e3bee;
            border-color: #5e3bee;
            color: #fff;
        }
        .forgot-password {
            color: #5e3bee;
        }
        .forgot-password:hover, .forgot-password:focus {
            color: #5e3bee;
            text-decoration: underline;
        }
    </style>
    <?php include "includes/header.php" ?>
    <body>
        <?php include "includes/nav.php" ?>
        <div class="container">
            <div class="row">
                <div class="col-md-6 col-md-offset-3">
                    <?php show_msg(); ?>
                    <div class="panel panel-login">
                        <div class="panel-heading">
                            <a href="#" class="active">Login</a>
                            <hr>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-lg-12">
                                    <form id="login-form" method="post" role="form" style="display: block;">
                                        <div class="form-group">
                                            <input type="text" name="username" id="username" tabindex="1"
                                                   class="form-control" placeholder="Username" value="<?php echo $username; ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <input type="password" name="password" id="login-password" tabindex="2"
                                                   class="form-control" placeholder="Password" value="<?php echo $password; ?>" required>
                                        </div>
                                        <div class="form-group text-center">
                                            <input type="checkbox" tabindex="3" name="remember" id="remember">
                                            <label for="remember">Stay logged in</label>
                                        </div>
                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-sm-6 col-sm-offset-3">
                                                    <input type="submit" name="login-submit" id="login-submit" tabindex="4" class="form-control btn btn-custom" value="Log In">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <div class="text-center">
                                                        <a href="reset_1.php" tabindex="5" class="forgot-password">Forgot Password?</a>
                                                    </div>
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