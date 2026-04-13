<?php
// config.php
$host = 'localhost';
$dbname = 'task_manager_db';
$username = 'root'; // Change if required
$password = ''; // Change if required

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    // Set the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Fetch attributes as associative arrays by default
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    // If connection fails, check if we just need to try connecting without DB to create it.
    // In a real app, you would handle this more gracefully.
    error_log("Connection failed: " . $e->getMessage());
    die("Database connection failed. Please ensure MySQL is running and task_manager_db exists.");
}
?>
