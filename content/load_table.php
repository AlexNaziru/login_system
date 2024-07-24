<?php
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
        $result = $pdo->query("SELECT {$fields} FROM {$table}{$where}{$order};");
        // Putting a title on the table modal
        if (isset($_POST["title"])) {
            $returnTable = "<h2 class='text-center'>{$_POST["title"]}</h2>";
        } else {
            $returnTable = "";
        }
        // Don't forget to append because the table class it's not the first thing added (.)
        $returnTable.= "<table class='table table-hover'>";
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

