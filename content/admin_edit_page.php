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
    $page_id = $_GET['id'];
    // Checking if that user id exists
    if (count_field_val($pdo, "pages", "id", $page_id) > 0) {
        $row = return_field_data($pdo, "pages", "id", $page_id);
        $name = $row['name'];
        $url = $row['url'];
        $group_id = $row['group_id'];
        $description = $row['description'];
    } else {
        redirect("admin.php");
    }
} else {
    redirect("admin.php");
}
// Editing and replacing the existing information and saving it to our db
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $url = $_POST['url'];
    $group_id = $_POST['group_id'];
    $description = $_POST['description'];

    // Negating isset in case there are no errors
    if (!isset($error)) {
        try { /* This statemant will submit to the database */
            $sql = "UPDATE pages SET name = :name, url = :url, group_id = :group_id, description = :description WHERE id = :id";
            $stmnt = $pdo->prepare($sql);
            $user_data = ['name' => $name, 'url' => $url, 'group_id' => $group_id,':description' => $description, ':id'=>$page_id];
            $stmnt->execute($user_data);
            redirect("admin.php?tab=pages");
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
                                                // Show as default the group the page is set up
                                                if ($row['id'] == $group_id) {
                                                    $selected = " selected";
                                                } else {
                                                    $selected = "";
                                                }
                                                echo "<option value={$row['id']}{$selected}> {$row['name']}</option>";
                                            }
                                        } catch (PDOException $exception) {
                                            echo "Error: ".$exception->getMessage();
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <textarea name="description" id="description" tabindex="7" class="form-control" placeholder="description"><?php echo $description ?></textarea>
                                </div>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-sm-6 col-sm-offset-3">
                                            <input type="submit" name="update-submit" id="update-submit" tabindex="4"
                                                   class="form-control btn btn-custom" value="Edit Page">
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

