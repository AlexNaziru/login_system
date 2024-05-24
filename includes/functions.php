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

// Email function
function send_email($to, $subject, $body, $from, $reply)
{
    $headers = "From: {$from}"."\r\n"."Reply-to: {$reply} "." \r\n "."X-Mailer:PHP/".phpversion();
    if ($_SERVER['SERVER_NAME'] != "localhost") {
        mail($to, $subject, $body, $headers);
    } else {
        echo "<hr><p>To: {$to}</p><p>Subject: {$subject}</p><p>{$body}</p><p>".$headers."</p><hr>";
    }
}

// Get validation code
function validation_code($user, $pdo)
{
    try {
        $stmnt = $pdo->prepare("SELECT validationcode FROM users WHERE username = :username");
        $stmnt->execute([':username'=>$user]);
        $row = $stmnt->fetch();
        return $row['validationcode'];
    } catch (PDOException $e) {
        return $e->getMessage();
    }
}