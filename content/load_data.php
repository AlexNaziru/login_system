<?php
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
    // PDO for PostgreSQL connection
    $dsn = "pgsql:host=localhost;dbname=login;port=5432";
    $opt = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_SILENT, # This won't give an error and it won't give away how our db is structured
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false
    ];
    $pdo = new PDO($dsn, 'postgres', 'alexandru', $opt);

// Loading the postGIS eagle data
    // Error handling
    try {
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
    } catch (PDOException $e) {
        echo "Error: ". $e->getMessage();
        }
} else {
    echo "Error: No table parameter";
}
