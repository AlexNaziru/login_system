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
                // adding for geojson data into our geom column in our db
                if ($key == "geojson") {
                    // We need to convert it from geojson to binary object.
                    // Geojson doesnt have special ref associated. we need to give special reference using st_setsrid
                    $sets.="geom = St_SetSRID(St_GeomFromGeoJSON(:geojson), 4326), ";
                } else {
                    // if not geojson data, it's just standard data
                    $sets.="{$key}=:{$key}, ";
                }
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
