<?php
require_once 'config/database.php';

try {
    $database = new Database();
    $db = $database->getConnection();
    
    // Read SQL file
    $sql = file_get_contents('gym_management_schema.sql');
    
    // Execute SQL file
    $db->exec($sql);
    
    echo "SQL file imported successfully!";
    
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?> 