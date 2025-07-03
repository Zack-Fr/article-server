<?php 
require("../connection/connection.php");


$query = "CREATE TABLE articles(
        id INT(11) AUTO_INCREMENT PRIMARY KEY, 
        name VARCHAR(255) NOT NULL, 
        author VARCHAR(255) NOT NULL, 
        description TEXT NOT NULL)";

$execute = $mysqli->prepare($query);
$execute->execute();