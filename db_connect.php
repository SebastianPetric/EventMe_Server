<?php

require_once 'db_config.php';
 
try {
    $db = new PDO("mysql:host=$DB_SERVER;dbname=$DB_DATABASE", $DB_USER , $DB_PASSWORD);
} catch (PDOException $error) {
    die("Could not connect to the database $dbname :" . $error->getMessage());
}
?>