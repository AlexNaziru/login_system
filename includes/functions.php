<?php
function redirect($location)
{
    header("Location: {$location}");
}
function generate_token()
{
    return md5(microtime().mt_rand());
}
function count_field_val($pdo, $tbl, $fld, $value)
{
    try {
        $sql = "SELECT {$fld} FROM {$tbl} WHERE {$fld}=:value";
        $stmnt = $pdo->prepare($sql);
        $stmnt->execute([":value" => $value]);
        return $stmnt->rowCount();
    } catch (PDOException $e) {
        return $e->getMessage();
    }
}