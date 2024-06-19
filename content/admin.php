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
<!DOCTYPE html>
<html lang="en">
    <?php include "../includes/header.php" ?>
    <body>
        <?php include "../includes/nav.php" ?>

        <div class="container">
            <?php show_msg(); ?>
            <h1 class="text-center">Admin Dashboard</h1>
            <ul class="nav nav-tabs">
                  <li id="users" class="tab-label active"><a href="#">Users</a></li>
                  <li id="groups" class="tab-label"><a href="#">Groups</a></li>
                  <li id="pages" class="tab-label"><a href="#">Pages</a></li>
            </ul>
            <div id='tab-users' class='tab-content'>
                <?php
                // the -> means there is a method after the object
                try {
                    $result = $pdo->query("SELECT id, firstname, lastname, username, email, active, joined, last_login FROM users ORDER BY username");
                    if ($result->rowCount() > 0) {
                        echo "<table class='table'>";
                        echo "<tr>
                    <th>ID</th>
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
                                // Reactivating user
                                $action = "Deactivate";
                            } else {
                                $active = "No";
                                $action = "Activate";
                            }
                            echo "<tr>
                      <td>{$row['id']}</td>
                      <td>{$row['firstname']}</td>
                      <td>{$row['lastname']}</td>
                      <td>{$row['username']}</td>
                      <td>{$row['email']}</td>
                      <td>{$active}</td>
                      <td>{$row['joined']}</td>
                      <td>{$row['last_login']}</td>
                      <td><a href='admin_deactivate_user.php?id={$row['id']}'>{$action} User</a></td>
                      <td><a href='admin_edit_user.php?id={$row['id']}'>Edit</a></td>
                      <td><a class='confirm-delete' href='admin_delete_user.php?id={$row['id']}&tbl=users'>Delete</a></td>
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
                    <th>Users</th>
                    <th>Pages</th>
                    </tr>";
                        foreach ($result as $row) {
                            // Showing numbers of user in each group
                            $user_count = count_field_val($pdo, "user_group_link", "group_id", $row["id"]);
                            $page_count = count_field_val($pdo, "pages", "group_id", $row["id"]);
                            echo "<tr>
                      <td>{$row['id']}</td>
                      <td>{$row['name']}</td>
                      <td>{$row['description']}</td>  
                      <td>{$user_count}</td>
                      <td>{$page_count}</td> 
                      <td><a href='admin_manage_users.php?id={$row['id']}'>Manage Users</a></td>
                      <td><a class='confirm-delete' href='admin_delete_user.php?id={$row['id']}&tbl=groups'>Delete</a></td> 
                      <td><a href='admin_edit_group.php?id={$row['id']}'>Edit</a></td>         
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
                    <th>Group</th>
                    <th>Description</th>
                    </tr>";
                        foreach ($result as $row) {
                            // We are going to look threw the db for the group_id and substitute it to the group name (to select it when making a group page)
                            $group_row = return_field_data($pdo, "groups", "id", $row["group_id"]);
                            echo "<tr>
                      <td>{$row['id']}</td>
                      <td>{$row['name']}</td>
                      <td>{$row['url']}</td>
                      <td>{$group_row['name']}</td>
                      <td>{$row['description']}</td>    
                      <td><a class='confirm-delete' href='admin_delete_user.php?id={$row['id']}&tbl=pages'>Delete</a></td>  
                      <td><a href='admin_edit_page.php?id={$row['id']}'>Edit</a></td>       
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
            // Confirm delete
            $(".confirm-delete").click(function(e) {
               if (!confirm("Are you sure you want to delete this record?")) {
                    e.preventDefault();
               }
            });
            /* This function is for the group tabs, if the user inserts anything, it will be redirected on the tab he was on */
            if (getParametersByName("tab")) {
                gotoTab(getParametersByName("tab"))
            } else {
                gotoTab("users");
            }
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