<?php
include "../../includes/init.php";
$username = $_SESSION["username"];
if (isset($_POST['tbl'])) {
    $table = $_POST['tbl'];
    if (isset($_POST['id'])) {
        $id = $_POST['id'];
        // Unseting the parameters
        unset($_POST["tbl"]);
        unset($_POST["id"]);

        try {
            $sets = "";
            foreach ($_POST as $key => $value) {
                $sets.="{$key}=:{$key}, ";
            }
            $sql = "UPDATE {$table} SET {$sets} modified = current_date, modifiedby = '{$username}' WHERE id = '{$id}' ";
            $result = $pdo->prepare($sql);
            $result->execute($_POST);
            echo "SUCCESS";
        } catch(PDOException $e) {
            echo "ERROR: ".$e->getMessage();
        }
    } else {
        echo "ERROR: ID not included in the delete request";
    }
} else {
    echo "ERROR: No table parameter incuded with request";
}
