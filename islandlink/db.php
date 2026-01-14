<?php
$conn = new mysqli("localhost", "root", "", "islandlink_db");

if ($conn->connect_error) {
    die("Database connection failed");
}

session_start();
?>
