<?php include("../includes/init.php");?>

<!-- Taking info from the form and adding it to the database -->
<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $description = $_POST['description'];

    if (strlen($name) < 3 ) {
        $error[] = "Group name must be at least 3 characters!";
    }
    if (!isset($error)) {
        try {
            $stmnt = $pdo->prepare("INSERT INTO groups (name, description) VALUES (:name, :description)");
            $stmnt->execute([":name"=>$name, ":description"=>$description]);
            set_msg("Group '{$name}' has been added", "success");
            redirect("admin.php?tab=groups");
        } catch (PDOException $exception) {
            echo "Error: ".$exception->getMessage();
        }
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
                                            <input type="text" name="name" id="name" tabindex="1" class="form-control" placeholder="Group Name" required >
                                        </div>
                                        <div class="form-group">
                                            <textarea name="description" id="description" tabindex="8" class="form-control" placeholder="Description - Tell us about your organization ?"></textarea>
                                        </div>
                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-sm-6 col-sm-offset-3">
                                                    <input type="submit" name="register-submit" id="register-submit" tabindex="4" class="form-control btn btn-custom" value="Add Group">
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
