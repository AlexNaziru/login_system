<?php
// Output buffering
ob_start();
// Session
session_start();
// PDO for PostgreSQL connection
$dsn = "pgsql:host=localhost;dbname=login;port=5432";
$opt = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, # This won't give an error and it won't give away how our db is structured
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false
];
$pdo = new PDO($dsn, 'postgres', 'alexandru', $opt);

// PDO for MySQL
// is the same but in $dsn = "mysql:host=localhost;dbname=login;port=3306;charset=utf8";
// the rest is identical but the username might be mysql by default

// Sending verification email variables
$root_directory = "login_system";
$from_email = "alex.naziru.dev@gmail.com";
$reply = "alex.naziru.dev@gmail.com";

include "functions.php";