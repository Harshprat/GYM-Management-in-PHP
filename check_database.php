<?php
require_once 'config/database.php';

try {
    $database = new Database();
    $db = $database->getConnection();
    
    echo "<h2>Database Connection Status:</h2>";
    echo "Connected successfully to database: " . $database->getDbName() . "<br><br>";
    
    // Get all tables
    $query = "SHOW TABLES";
    $stmt = $db->query($query);
    
    echo "<h2>Tables in Database:</h2>";
    if ($stmt->rowCount() > 0) {
        echo "<ul>";
        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            echo "<li>" . $row[0] . "</li>";
        }
        echo "</ul>";
    } else {
        echo "No tables found in the database.";
    }
    
} catch(PDOException $e) {
    echo "Connection Error: " . $e->getMessage();
}
?> 