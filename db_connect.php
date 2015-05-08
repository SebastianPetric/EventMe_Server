<?php

require_once 'db_config.php';
 
try {
    $db = new PDO("mysql:host=$DB_SERVER;dbname=$DB_DATABASE", $DB_USER , $DB_PASSWORD);
   	//$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $error) {
    die("Could not connect to the database $dbname :" . $error->getMessage());
}
?>