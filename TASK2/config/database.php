<?php

$host = '127.0.0.1';
$db   = 'project_management';
$user = 'root';
$pass = '';
$ports = [3306, 3307];

$conn = null;
$lastError = null;

foreach ($ports as $port) {
    $candidate = @new mysqli($host, $user, $pass, $db, $port);
    if (!$candidate->connect_error) {
        $conn = $candidate;
        break;
    }
    $lastError = $candidate->connect_error;
}

if ($conn === null) {
    die('Connection failed: ' . ($lastError ?? 'unknown'));
}

$conn->set_charset('utf8mb4');
