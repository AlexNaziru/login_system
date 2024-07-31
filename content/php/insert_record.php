<?php
include "../../includes/init.php";
$username = $_SESSION["username"];
if (isset($_POST['tbl'])) {
    $table = $_POST['tbl'];
    // Unseting the parameters
    unset($_POST["tbl"]);


    try {
        $keys = "";
        $vals = "";
        foreach ($_POST as $key => $value) {
            $keys.="{$key}, ";
            // the : indicates a placeholder for the value
            $vals.=":{$key}, ";
        }
        $sql = "INSERT INTO {$table} ({$keys}created, createdby, modified, modifiedby) 
                VALUES ({$vals}current_date, '{$username}', current_date, '{$username}')";
        $result = $pdo->prepare($sql);
        $result->execute($_POST);
        echo "SUCCESS";
    } catch(PDOException $e) {
        echo "ERROR: ".$e->getMessage();
    }
} else {
    echo "ERROR: No table parameter incuded with request";
}