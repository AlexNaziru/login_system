<?php include("../includes/init.php");?>

<?php
//If they are logged in, they can see this page
if (logged_in()) {
    $username = $_SESSION["username"];
    error_log("Logged in user: " . $username);
    // Checking to see if the user is a member of the group
    if (verify_user_group($pdo, $username, "Admin")) {
        set_msg("User '{$username}' does not have permission to view this page");
        error_log("User '{$username}' does not have permission to view this page");
        redirect("../index.php");
    }
} else {
    set_msg("Please log in and try again!");
    error_log("User is not logged in.");
    // If they are not logged in, they won't
    redirect("../index.php");
}
?>
?>

<!-- Taking info from the form and adding it to the database -->
<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $url = $_POST['url'];
    $group_id = $_POST['group_id'];

    if (strlen($name) < 3 ) {
        $error[] = "Group name must be at least 3 characters!";
    }
    if (strlen($url) < 3 ) {
        $error[] = "URL must be at least 3 characters.";
    }
    // Error check to see if the group ID they enter is valid
    if (count_field_val($pdo, "groups", "id", $group_id) == 0) {
        $error[] = "Group ID not available";
    }
    if (!isset($error)) {
        try {
            $stmnt = $pdo->prepare("INSERT INTO pages (name, description, url, group_id) VALUES (:name, :description, :url, :group_id)");
            $stmnt->execute([":name"=>$name, ":description"=>$description, ":url"=>$url, ":group_id"=>$group_id]);
            set_msg("Page '{$name}' has been added", "success");
            redirect("admin.php?tab=pages ");
        } catch (PDOException $exception) {
            echo "Error: ".$exception->getMessage();
        }
    }
} else {
    $name = "";
    $description = "";
    $url = "";
    $group_id = "";
}

?>
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
                                            <input type="text" name="name" id="name" tabindex="1" class="form-control" placeholder="Page Name"
                                                   value="<?php echo $name ?>" required >
                                        </div>
                                        <div class="form-group">
                                            <input type="text" name="url" id="url" tabindex="2" class="form-control" placeholder="Page URL"
                                                   value="<?php echo $url?>" required >
                                        </div>
                                        <div class="form-group">
                                            <select name="group_id" id="group_id" class="form-control" required>
                                                <!-- Dynamic group id's to select and not hardcode-->
                                                <?php
                                                    try {
                                                        /* Querying the database */
                                                        $result = $pdo->query("SELECT id, name FROM groups ORDER BY name");
                                                        // Looping threw the results
                                                        foreach ($result as $row) {
                                                            echo "<option value={$row['id']}> {$row['name']}</option>";
                                                        }
                                                    } catch (PDOException $exception) {
                                                        echo "Error: ".$exception->getMessage();
                                                    }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <textarea name="description" id="description" tabindex="8" class="form-control"
                                                      placeholder="Description - Tell us about your organization ?"><?php echo $description ?></textarea>
                                        </div>
                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-sm-6 col-sm-offset-3">
                                                    <input type="submit" name="register-submit" id="register-submit" tabindex="4" class="form-control btn btn-custom" value="Add Page">
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
