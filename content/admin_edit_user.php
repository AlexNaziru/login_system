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
// This just fills up the edit form with user data based on it's id
if (isset($_GET['id'])) {
    $user_id = $_GET['id'];
    // Checking if that user id exists
    if (count_field_val($pdo, "users", "id", $user_id) > 0) {
        $row = return_field_data($pdo, "users", "id", $user_id);
        $fname = $row['firstname'];
        $lname = $row['lastname'];
        $comments = $row['comments'];
    } else {
        redirect("admin.php");
    }
} else {
    redirect("admin.php");
}
// Editing the existing information and saving it to our db
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fname = $_POST['firstname'];
    $lname = $_POST['lastname'];
    $comments = $_POST['comments'];

    // Validation Code
    if (strlen($lname) < 3) {
        // array of error messages
        $error[] = "Lastname must be at least 3 characters long!";
    }
    // Querying the db to see if there are duplicate usernames
    if (count_field_val($pdo, "users", "username", $uname) != 0) {
        $error[] = "Username '{$uname}' already exists.";
    }

    // Negating isset in case there are no errors
    if (!isset($error)) {
        try { /* This statemant will submit to the database */
            $sql = "UPDATE users SET firstname = :firstname, lastname = :lastname, comments = :comments WHERE id = :id";
            $stmnt = $pdo->prepare($sql);
            $user_data = [':firstname' => $fname, ':lastname' => $lname, ':comments' => $comments, ':id'=>$user_id];
            $stmnt->execute($user_data);
            redirect("admin.php");
        } catch (PDOException $exception) {
            echo "Error: " . $exception->getMessage();
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<?php include "../includes/header.php" ?>
<body>
<?php include "../includes/nav.php" ?>

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
                                            <textarea name="comments" id="comments" tabindex="7" class="form-control" placeholder="Comments">
                                                <?php echo $comments ?>
                                            </textarea>
                                </div>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-sm-6 col-sm-offset-3">
                                            <input type="submit" name="update-submit" id="update-submit" tabindex="4" class="form-control btn btn-custom" value="Update user">
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
