<?php

$host = "127.0.0.1:3308";
$user = "root";
$password = "";
$database = "project2026";

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: ". $conn->connect_error);
}

?>