<?php
// Database connecting function code.
function DB_connection(){
    $serverName = "localhost";
    $userName = "root";
    $password = "";
    $databaseName = "task_manager";

    $conn = new mysqli($serverName, $userName, $password, $databaseName);
    
    if ($conn->connect_error) {
        // Handle database connection error gracefully
        die("Database Connection Failed: " . $conn->connect_error);
    }
    return $conn;
}
?>