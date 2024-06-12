<?php include("../includes/init.php");?>
<!DOCTYPE html>
<html lang="en">
    <?php include "../includes/header.php" ?>
    <body>
        <?php include "../includes/nav.php" ?>

        <div class="container">
            <h1 class="text-center">Admin</h1>
            <ul class="nav nav-tabs">
                  <li id="users" class="tab-label active"><a href="#">Users</a></li>
                  <li id="groups" class="tab-label"><a href="#">Groups</a></li>
                  <li id="pages" class="tab-label"><a href="#">Pages</a></li>
            </ul>
            <div id='tab-users' class='tab-content'>
                <?php
                // the -> means there is a method after the object
                try {
                    $result = $pdo->query("SELECT firstname, lastname, username, email, active, joined, last_login FROM users ORDER BY username");
                    if ($result->rowCount() > 0) {
                        echo "<table class='table'>";
                        echo "<tr>
                    <th>Firstname</th>
                    <th>Lastname</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Active</th>
                    <th>Joined</th>
                    <th>Last Login</th>
                    </tr>";
                        foreach ($result as $row) {
                            if ($row['active']) {
                                $active = "Yes";
                            } else {
                                $active = "No";
                            }
                            echo "<tr>
                      <td>{$row['firstname']}</td>
                      <td>{$row['lastname']}</td>
                      <td>{$row['username']}</td>
                      <td>{$row['email']}</td>
                      <td>{$active}</td>
                      <td>{$row['joined']}</td>
                      <td>{$row['last_login']}</td>
                      </tr>"."<br>";
                        }
                        echo "</table>";
                    } else {
                        echo "No users";
                    }
                } catch (PDOException $e) {
                    echo "Something went wrong<br><br>".$e->getMessage();
                }

                ?>
            </div>
            <div id='tab-groups' class='tab-content'>
                <?php
                // the -> means there is a method after the object
                try {
                    $result = $pdo->query("SELECT id, name, description FROM groups ORDER BY name");
                    if ($result->rowCount() > 0) {
                        echo "<table class='table'>";
                        echo "<tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Description</th>
                    </tr>";
                        foreach ($result as $row) {
                            echo "<tr>
                      <td>{$row['id']}</td>
                      <td>{$row['name']}</td>
                      <td>{$row['description']}</td>             
                      </tr>"."<br>";
                        }
                        echo "</table>";
                    } else {
                        echo "No groups in table <br>";
                    }
                } catch (PDOException $e) {
                    echo "Something went wrong<br><br>".$e->getMessage();
                }

                ?>
                <a href='admin_add_group.php' class="btn btn-success">Add Group</a>
            </div>
            <div id='tab-pages' class='tab-content'>
                <?php
                // the -> means there is a method after the object
                try {
                    $result = $pdo->query("SELECT id, name, url, group_id, description FROM pages ORDER BY name");
                    if ($result->rowCount() > 0) {
                        echo "<table class='table'>";
                        echo "<tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>URL</th>
                    <th>Group ID</th>
                    <th>Description</th>
                    </tr>";
                        foreach ($result as $row) {
                            echo "<tr>
                      <td>{$row['id']}</td>
                      <td>{$row['name']}</td>
                      <td>{$row['url']}</td>
                      <td>{$row['group_id']}</td>
                      <td>{$row['description']}</td>             
                      </tr>"."<br>";
                        }
                        echo "</table>";
                    } else {
                        echo "No pages in table <br>";
                    }
                } catch (PDOException $e) {
                    echo "Something went wrong<br><br>".$e->getMessage();
                }

                ?>
                <a href='admin_add_page.php' class="btn btn-success">Add Page</a>
            </div>
        </div> <!--Container-->
        <?php include "../includes/footer.php" ?>
        <script>
            gotoTab("users");
            $(".tab-label").click(function(){
                gotoTab($(this).attr('id'));
            });
            function gotoTab(label){
                var current_tab="#tab-"+label;
                console.log("'"+current_tab+"'");
                $(".tab-content").hide();
                $(".tab-label").removeClass("active");
                $(current_tab).show();
                $("#"+label).addClass("active");
            }
        </script>
    </body>
</html>