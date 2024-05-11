<?php
// PDO for PostgreSQL connection
$dsn = "pgsql:host=localhost;dbname=login;port=5432";
$opt = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false
];
$pdo = new PDO($dsn, 'postgres', 'alexandru', $opt);
echo "Connected to DB";

// PDO for MySQL
// is the same but in $dsn = "mysql:host=localhost;dbname=login;port=3306;charset=utf8";
// the rest is identical but the username might be mysql by default

