<?php
include "../../includes/init.php";
if (isset($_POST['tbl'])) {
    $table = $_POST['tbl'];
    if (isset($_POST['fld'])) {
        $fld = $_POST['fld'];

        try {
            $result = $pdo->query("SELECT DISTINCT {$fld} FROM {$table} ORDER BY {$fld}");

            $returnOptions = "";
            foreach($result AS $row) {
                $returnOptions.= "<option value='{$row[$fld]}'>{$row[$fld]}</option>";
            }

            echo $returnOptions;
        } catch(PDOException $e) {
            echo "ERROR: ".$e->getMessage();
        }
    } else {
        echo "ERROR: Field not included with request";
    }
} else {
    echo "ERROR: No field parameter included with request";
}