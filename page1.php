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
            <p>"Develop complex web GIS applications using data stored in a PostGIS database with open source software that doesn't require any licensing fees or subscriptions"</p>
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

