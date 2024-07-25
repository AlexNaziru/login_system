<?php
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
        // Searching for BUOWL
        // Quering spacial data and non-special data by combining queries
        $strQuery = 'SELECT Round(ST_Distance(b.geom::geography, l.geom::geography)) as dist, b.habitat_id as id, b.recentstatus as status, 
                     Round(ST_Area(b.geom::geography)/1000)/10 as hectares 
                     FROM dj_buowl b 
                     JOIN dj_linear_projects l 
                     ON ST_DWithin(b.geom::geography, l.geom::geography, 300) 
                     WHERE l.project='.$id.' AND ST_Area(b.geom)>0.000000001 
                     ORDER BY dist';

        $result = $pdo->query($strQuery);

        // Don't forget to append because the table class it's not the first thing added (.)
        $returnTable = "<table class='table table-hover'>";

        $returnTable.="<tr><th>Constraint</th><th>ID</th><th>Distance</th><th>Status</th><th>Hectares</th></tr>";

        foreach ($result AS $row) {
            // Getting data values in html format
            $returnTable.="<tr><td>BUOWL</td><td>{$row["id"]}</td><td>{$row["dist"]}</td>
                           <td>{$row["status"]}</td><td>{$row["hectares"]}</td></tr>";
        }
        echo $returnTable;

        // Searching for eagles

        $strQuery = 'SELECT round(st_distance(b.geom::geography, l.geom::geography)) as dist, b.nest_id as id,
        b.status as status
        FROM dj_eagle b
        JOIN dj_linear_projects l
        ON st_dwithin(b.geom::geography, l.geom::geography, 804.5)
        ORDER BY "dist";';

        $result = $pdo->query($strQuery);

        foreach ($result AS $row) {
            // Getting data values in html format
            $returnTable.="<tr><td>Eagle</td><td>{$row["id"]}</td><td>{$row["dist"]}</td>
                           <td>{$row["status"]}</td><td>N/A</td></tr>";
        }

        // Searching for Raptors

        // Because we have multiple distances, we need to adapt our queries in reference to the species
        $case = "CASE 
            WHEN b.recentspecies = 'Swainsons Hawk' THEN 402 
            WHEN b.recentspecies = 'Red-tail Hawk' THEN 533 
            ELSE 1600 
        END";

        $strQuery = 'SELECT Round(ST_Distance(b.geom::geography, l.geom::geography)) as dist, b.nest_id as id, b.recentstatus as status 
                     FROM dj_raptor b 
                     JOIN dj_linear_projects l ON ST_DWithin(b.geom::geography, l.geom::geography, '.$case.')
                     WHERE l.project='.$id.' 
                     ORDER BY dist';

        $result = $pdo->query($strQuery);

        foreach ($result AS $row) {
            // Getting data values in html format
            $returnTable.="<tr><td>Raptor</td><td>{$row["id"]}</td><td>{$row["dist"]}</td>
                           <td>{$row["status"]}</td><td>N/A</td></tr>";
        }
        echo $returnTable;
    } catch (PDOException $e) {
        echo "Error: ". $e->getMessage();
    }
