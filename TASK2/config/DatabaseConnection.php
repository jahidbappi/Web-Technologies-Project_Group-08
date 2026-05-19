<?php

$host = "127.0.0.1";
$username = "root";
$password = "";
$database = "project_management";
$port = 3307;

$conn = new mysqli($host, $username, $password, $database, $port);

if ($conn->connect_error) {
    die("Connection Failed: " . $conn->connect_error);
}

?>