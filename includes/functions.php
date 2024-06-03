<?php
function redirect($location)
{
    header("Location: {$location}");
}

function generate_token()
{
    return md5(microtime().mt_rand());
}

// Logged in
function logged_in()
{
    if (isset($_SESSION["username"])) {
        return true;
    } else {
        // If the session is not set, we have to check for the cookie that is set
        if (isset($_COOKIE["username"])) {

            // we are also going to start a session
            $_SESSION["username"] = $_COOKIE["username"];
            return true;

            // if there is no session and no cookie, means we are not logged in and we redirect
        } else {
            // If they are not logged in, they won't
            return false;
        }
    }
}

// Messaging system
function set_msg($msg, $level="danger")
{
    // If it's not a valid bootstrap level
    if (($level != "primary") && ($level != "success") && ($level != "info") && ($level != "warning")) {
        $level = "danger";
    }
    if (empty($msg)) {
        unset($_SESSION["message"]);
    } else {
        $_SESSION["message"] = "<h4 class='bg-{$level} text-center'>{$msg}</h4>";
    }
}

// show message function
function show_msg()
{
    if (isset($_SESSION["message"])) {
        echo $_SESSION["message"];
        unset($_SESSION["message"]);
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

// ************ Database Functions ****************

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

// Returns all the data
function return_field_data($pdo, $tbl, $fld, $value)
{
    try {
        $sql = "SELECT * FROM {$tbl} WHERE {$fld}=:value";
        $stmnt = $pdo->prepare($sql);
        $stmnt->execute([":value" => $value]);
        return $stmnt->fetch(PDO::FETCH_ASSOC); // Instead of rowCount, we are returning the entire row as in the fetch method
    } catch (PDOException $e) {
        error_log('Database error in return_field_data(): ' . $e->getMessage());
        return $e->getMessage();
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

