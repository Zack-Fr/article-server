<?php 
require("../connection/connection.php");


$query = "ALTER TABLE articles....";

$execute = $mysqli->prepare($query);
$execute->execute();