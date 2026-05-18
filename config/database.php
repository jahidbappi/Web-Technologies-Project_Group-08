<?php

$conn = new mysqli(
    "127.0.0.1:3307",
    "root",
    "",
    "project_management"
);

if($conn->connect_error)
{
    die("Connection Failed");
}

?>