<?php

$hostname = "localhost";
$username = "USERNAME";
$dbname = "auction";
$password = "PASSWORD";

$mysqli = new mysqli($hostname, $username, $password, $dbname);
if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
}

?>