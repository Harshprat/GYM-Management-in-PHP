<?php
session_start();
require_once '../config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Unauthorized access'
    ]);
    exit();
}

$database = new Database();
$db = $database->getConnection();

// Handle different HTTP methods
switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        // Get all plans
        $query = "SELECT * FROM membership_plans ORDER BY id DESC";
        $stmt = $db->query($query);
        $plans = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode([
            'success' => true,
            'plans' => $plans
        ]);
        break;

    case 'POST':
        // Add new plan
        $query = "INSERT INTO membership_plans (name, description, price, duration) 
                 VALUES (:name, :description, :price, :duration)";
        
        $stmt = $db->prepare($query);
        $stmt->bindParam(':name', $_POST['plan_name']);
        $stmt->bindParam(':description', $_POST['description']);
        $stmt->bindParam(':price', $_POST['price']);
        $stmt->bindParam(':duration', $_POST['duration']);
        
        if ($stmt->execute()) {
            echo json_encode([
                'success' => true,
                'message' => 'Plan added successfully'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Failed to add plan'
            ]);
        }
        break;

    case 'DELETE':
        if (isset($_GET['id'])) {
            $query = "DELETE FROM membership_plans WHERE id = :id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':id', $_GET['id']);
            
            if ($stmt->execute()) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Plan deleted successfully'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Failed to delete plan'
                ]);
            }
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Plan ID is required'
            ]);
        }
        break;
}
?> 