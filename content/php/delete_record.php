<?php
include "../../includes/init.php";
if (isset($_POST['tbl'])) {
    $table = $_POST['tbl'];
    if (isset($_POST['id'])) {
        $id = $_POST['id'];


        try {
            $result = $pdo->query('DELETE FROM '.$table.' WHERE id ='.$id); // The buttons mean to concatenate in php
            echo "Success";
        } catch(PDOException $e) {
            echo "ERROR: ".$e->getMessage();
        }
    } else {
        echo "ERROR: ID not included in the delete request";
    }
} else {
    echo "ERROR: No table parameter incuded with request";
}
