<?php
if (isset($_POST["tbl"])) {
    $table = $_POST["tbl"];
    if (isset($_POST["fld"])) {
        $fields = $_POST["fld"];
    } else {
        $fields = "nest_id";
    }
    if (isset($_POST["distance"])) {
        $distance = $_POST["distance"];
    } else {
        $distance = 300; // by default
    }
    if (isset($_POST["id"])) {
        $id = $_POST["id"];
    } else {
        $id = 1;
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
        // Quering spacial data and non-special data by combining queries
        $strQuery = 'SELECT round(st_distance(b.geom::geography, l.geom::geography)) as "Distance", l.project as "Project ID",
        l.type as "Type", round(st_length(l.geom::geography)) as "Length (m)"
        FROM '.$table.' b
        JOIN dj_linear_projects l
        ON st_dwithin(b.geom::geography, l.geom::geography, '.$distance.')
        WHERE '.$fields.' = '.$id.'
        ORDER BY "Distance"';
        $result = $pdo->query($strQuery);

        // Don't forget to append because the table class it's not the first thing added (.)
        $returnTable = "<table class='table table-hover'>";
        $row = $result->fetch();
        // Want only the 1st row for the key or headers
        if ($row) {
            // Getting field names keys in html format
            $returnTable.="<tr class='tblHeader'>";
            foreach ($row AS $key=>$val) {
                $returnTable.="<th>{$key}</th>";
            }
            $returnTable.="</tr>";
            // Getting data in html format
            $returnTable.="<tr>";
            foreach ($row AS $key=>$val) {
                $returnTable.="<td>{$val}</td>";
            }
            $returnTable.="</tr>";
        }
        foreach ($result AS $row) {
            // Getting data values in html format
            $returnTable.="<tr>";
            foreach ($row AS $key=>$val) {
                $returnTable.="<td>{$val}</td>";
            }
            $returnTable.="</tr>";
        }
        $returnTable.="</table>";
        echo $returnTable;
    } catch (PDOException $e) {
        echo "Error: ". $e->getMessage();
    }
} else {
    echo "Error: No table parameter";
}