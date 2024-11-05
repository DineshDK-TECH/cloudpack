<?php
// Database connection settings for local development
$host = 'localhost';       // Host
$dbname = 'task_manager';  // Database name
$username = 'root';        // MySQL username
$password = '';            // MySQL password (empty by default for XAMPP)

// Attempt to create a connection to the database
try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->exec("set names utf8");  // Set character encoding to UTF-8
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
