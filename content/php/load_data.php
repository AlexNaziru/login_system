<?php
include "../../includes/init.php";
// Getting all the postGIS data
if (isset($_POST["tbl"])) {
    $table = $_POST["tbl"];
    if (isset($_POST["flds"])) {
        $fields = $_POST["flds"];
    } else {
        $fields = "*";
    }
    if (isset($_POST["where"])) {
        $where = " WHERE ".$_POST["where"];
    } else {
        $where = "";
    }
    if (isset($_POST["order"])) {
        $order = " ORDER BY ".$_POST["order"];
    } else {
        $order = "";
    }
    // for editing surveys
    if (isset($_POST["spatial"])) {
        if ($_POST["spatial"] == "NO") {
            $spatial = "NO";
        } else {
            $spatial = "YES";
        }
    } else {
        $spatial = "YES";
    }

// Loading the postGIS eagle data
    // Error handling
    try {
        // Checking spatial data
        if ($spatial == "NO") {
            $features = [];
            $result = $pdo->query("SELECT {$fields} FROM {$table}{$where}{$order};");
            foreach ($result AS $row) {
                $feature = ["properties"=>$row];
                array_push($features, $feature);
            }
            echo json_encode($features);
        } else {
            // sending a buffer instead of the data to the client. the data is processed on the server
            if (isset($_POST["distance"])) {
                $result = $pdo->query("SELECT {$fields}, st_asgeojson(st_transform(st_buffer(
                    st_transform(geom, 26913), /* here is the distance*/ {$_POST["distance"]}), 4326), 5) AS geojson FROM {$table}{$where}{$order};");
            } else {
                $result = $pdo->query("SELECT {$fields}, st_asgeojson(geom, 5) AS geojson FROM {$table}{$where}{$order};");
            }
            // Putting all the features into an array
            $features = [];
            foreach ($result AS $row) {
                // unset removes the string from the postgis data set
                unset($row["geom"]);
                $row["geojson"] = $geometry = json_decode($row["geojson"]);
                unset($row["geojson"]);
                $feature = ["type" => "Feature", "geometry" => $geometry, "properties" => $row];
                array_push($features, $feature);
            }
            $featureCollection = ["type" => "FeatureCollection", "features" => $features];
            echo json_encode($featureCollection);
        }
    } catch (PDOException $e) {
        echo "Error: ". $e->getMessage();
        }
} else {
    echo "Error: No table parameter";
}
