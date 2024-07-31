<?php
if (isset($_POST['tbl'])) {
    $table = $_POST['tbl'];
    if (isset($_POST['fld'])) {
        $fld = $_POST['id'];
        $dsn = "pgsql:host=localhost;dbname=login;port=5432";
        $opt = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false
        ];
        $pdo = new PDO($dsn, 'postgres', 'alexandru', $opt);

        try {
            $result = $pdo->query('SELECT DISTINCT {$fld} FROM {$table}');

            $returnOptions = "";
            foreach($result AS $row) {
                $returnOptions."<option value='{$row[$fld]}'>{$row[$fld]}</option>";
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