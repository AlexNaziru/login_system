<?php include "includes/init.php"?>
<!DOCTYPE html>
<html lang="en">
    <style>
        .table-container {
            margin-top: 50px;
        }
        .table {
            margin: 0 auto;
            width: 80%; /* Adjust the width as needed */
        }
    </style>
    <?php include "includes/header.php" ?>
    <body>
        <?php include "includes/nav.php" ?>

        <div class="container table-container">
            <div class="row">
                <div class="col-md-8 col-md-offset-2">
            <?php
            show_msg();
            ?>
            <h1 class="text-center">Page 1</h1>
            <p>"Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer nec odio. Praesent libero. Sed cursus ante dapibus diam. Sed nisi. Nulla quis sem at nibh elementum imperdiet. Duis sagittis ipsum. Praesent mauris. Fusce nec tellus sed augue semper porta. Mauris massa. Vestibulum lacinia arcu eget nulla. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. "</p>
                <!--Container-->
                    <?php
                    // the -> means there is a method after the object
                    try {
                        $result = $pdo->query("SELECT firstname, lastname, username, password FROM users");
                        if ($result->rowCount() > 0) {
                            echo "<table class='table table-bordered table-striped'>";
                            echo "<thead>
                                <tr>
                                    <th>Firstname</th>
                                    <th>Lastname</th>
                                    <th>Username</th>
                                    <th>Password</th>
                                </tr>
                              </thead>
                              <tbody>";
                            foreach ($result as $row) {
                                echo "<tr>
                                    <td>{$row['firstname']}</td>
                                    <td>{$row['lastname']}</td>
                                    <td>{$row['username']}</td>
                                    <td>{$row['password']}</td>
                                  </tr>";
                            }
                            echo "</tbody></table>";
                        } else {
                            echo "<div class='alert alert-warning'>No users found</div>";
                        }
                    } catch (PDOException $e) {
                        echo "<div class='alert alert-danger'>Something went wrong<br><br>" . $e->getMessage() . "</div>";
                    }
                    ?>
                </div>
            </div>
        </div>
        
        <?php include "includes/footer.php" ?>
    </body>
</html>

