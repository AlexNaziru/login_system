<?php include "includes/init.php"?>
<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try { /* This statemant will submit to the database */
        $sql = "INSERT INTO users (firstname, lastname, username, password, validationcode, email, comments, joined, last_login) 
                /* Prepared statemants */    
                VALUES (:firstname, :lastname, :username, :password, 'test', :email, :comments, current_date, current_date)";
                $stmnt = $pdo->prepare($sql);
                $user_data = [':firstname'=>$_POST['firstname'], ':lastname'=>$_POST['lastname'], ':username'=>$_POST['username'],
                                ':password'=>$_POST['password'], ':email'=>$_POST['email'], ':comments'=>$_POST['comments']];
                $stmnt->execute($user_data);
                echo "Successfully submitted";
    } catch (PDOException $exception) {
        echo "Error: ".$exception->getMessage();
    }
} else {
    echo "No Post data included";
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
                                            <input type="text" name="firstname" id="firstname" tabindex="1" class="form-control" placeholder="First Name" value="" required >
                                        </div>
                                        <div class="form-group">
                                            <input type="text" name="lastname" id="lastname" tabindex="2" class="form-control" placeholder="Last Name" value="" required >
                                        </div>
                                        <div class="form-group">
                                            <input type="text" name="username" id="username" tabindex="3" class="form-control" placeholder="Username" value="" required >
                                        </div>
                                        <div class="form-group">
                                            <input type="email" name="email" id="register_email" tabindex="4" class="form-control" placeholder="Email Address" value="" required >
                                        </div>
                                        <div class="form-group">
                                            <input type="password" name="password" id="password" tabindex="5" class="form-control" placeholder="Password" required>
                                        </div>
                                        <div class="form-group">
                                            <input type="password" name="confirm_password" id="confirm-password" tabindex="6" class="form-control" placeholder="Confirm Password" required>
                                        </div>
                                        <div class="form-group">
                                            <textarea name="comments" id="comments" tabindex="7" class="form-control" placeholder="Comments"></textarea>
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