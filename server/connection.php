<?php
// Database configuration
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'avengerstutoring_database';

// Create a connection
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");