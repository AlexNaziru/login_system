<?php
if (isset($_POST['tbl'])) {
    $table = $_POST['tbl'];
    if (isset($_POST['id'])) {
        $id = $_POST['id'];
        $dsn = "pgsql:host=localhost;dbname=login;port=5432";
        $opt = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false
        ];
        $pdo = new PDO($dsn, 'postgres', 'alexandru', $opt);

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
