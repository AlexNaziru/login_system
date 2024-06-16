<?php include "includes/init.php"?>

<?php
//If they are logged in, they can see this page
if (logged_in()) {
    $username = $_SESSION["username"];
} else {
    // If they are not logged in, they won't
    redirect("index.php");
}
?>
<!DOCTYPE html>
<html lang="en">
<?php include "includes/header.php" ?>
<body>
<?php include "includes/nav.php" ?>

<div class="container">
    <?php
    show_msg();
    ?>
    <h1 class="text-center"> <?php echo $username;?>'s content</h1>
    <?php // Adding content from more tables to display
    try {
        $sql = "SELECT u.username, g.name AS group_name, g.description AS group_description, p.name AS page_name, p.description AS page_description, p.url ";
        $sql .= "FROM users u JOIN user_group_link gu ON u.id = gu.user_id ";
        $sql .= "JOIN groups g ON gu.group_id = g.id ";
        $sql .= "JOIN pages p ON g.id = p.group_id ";
        $sql .= "WHERE username = '{$username}' ";
        $sql .= "ORDER BY group_name ";
        $result = $pdo->query($sql);
        if ($result->rowCount() > 0) {
            echo "<table class='table'>";
            echo "<tr>
            <th>Group name</th>
            <th>Group description</th>
            <th>Page name</th>
            <th>Page description</th>
            <th>URL</th>
            </tr>";
            foreach ($result as $row) {
                echo "<tr>
                <td>{$row['group_name']}</td>
                <td>{$row['group_description']}</td>
                <td>{$row['page_name']}</td>
                <td>{$row['page_description']}</td>
                <td>{$row['url']}</td>
                </tr>";
            }
            echo "</table>";
        } else {
            echo "<h4>No content for {$username} available to display!</h4>";
        }
    } catch (PDOException $exception) {
        echo "There was an error on <br><br>".$exception->getMessage();
    }
    ?>
</div> <!--Container-->

<?php include "includes/footer.php" ?>
</body>
</html>