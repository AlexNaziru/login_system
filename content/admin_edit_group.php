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
    $group_id = $_GET['id'];
    // Checking if that user id exists
    if (count_field_val($pdo, "groups", "id", $group_id) > 0) {
        $row = return_field_data($pdo, "groups", "id", $group_id);
        $description = $row['description'];
    } else {
        redirect("admin.php");
    }
} else {
    redirect("admin.php");
}
// Editing the existing information and saving it to our db
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $description = $_POST['description'];

    // Negating isset in case there are no errors
    if (!isset($error)) {
        try { /* This statemant will submit to the database */
            $sql = "UPDATE groups SET description = :description WHERE id = :id";
            $stmnt = $pdo->prepare($sql);
            $user_data = [':description' => $description, ':id'=>$group_id];
            $stmnt->execute($user_data);
            redirect("admin.php?tab=groups");
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
                                <textarea name="description" id="description" tabindex="7" class="form-control" placeholder="description"><?php echo $description ?></textarea>
                                </div>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-sm-6 col-sm-offset-3">
                                            <input type="submit" name="update-submit" id="update-submit" tabindex="4"
                                                   class="form-control btn btn-custom" value="Edit Group">
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
