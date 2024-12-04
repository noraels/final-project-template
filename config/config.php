<?php
// config.php
$host = getenv('DBHOST');
$dbname = getenv('DBNAME');
$username = getenv('DBUSER');
$password = getenv('DBPASS');

// In config.php
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}

?>
