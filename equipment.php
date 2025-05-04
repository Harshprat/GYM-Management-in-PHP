<?php
session_start();
require_once '../config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

$database = new Database();
$db = $database->getConnection();

// Handle different actions
$action = $_POST['action'] ?? '';

switch ($action) {
    case 'update':
        // Update equipment
        if (isset($_POST['id'])) {
            try {
                $query = "UPDATE equipment SET 
                    name = :name,
                    description = :description,
                    quantity = :quantity,
                    status = :status
                    WHERE id = :id";
                
                $stmt = $db->prepare($query);
                $stmt->bindParam(':id', $_POST['id']);
                $stmt->bindParam(':name', $_POST['name']);
                $stmt->bindParam(':description', $_POST['description']);
                $stmt->bindParam(':quantity', $_POST['quantity']);
                $stmt->bindParam(':status', $_POST['status']);
                
                if ($stmt->execute()) {
                    echo json_encode(['success' => true, 'message' => 'Equipment updated successfully']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Failed to update equipment']);
                }
            } catch (PDOException $e) {
                echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
            }
        }
        break;

    case 'delete':
        // Delete equipment
        if (isset($_POST['id'])) {
            try {
                $query = "DELETE FROM equipment WHERE id = :id";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':id', $_POST['id']);
                
                if ($stmt->execute()) {
                    echo json_encode(['success' => true, 'message' => 'Equipment deleted successfully']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Failed to delete equipment']);
                }
            } catch (PDOException $e) {
                echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
            }
        }
        break;

    default:
        // Get single equipment or list all equipment
        if (isset($_GET['id'])) {
            try {
                $query = "SELECT * FROM equipment WHERE id = :id";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':id', $_GET['id']);
                $stmt->execute();
                
                if ($stmt->rowCount() > 0) {
                    $equipment = $stmt->fetch(PDO::FETCH_ASSOC);
                    echo json_encode(['success' => true, 'data' => $equipment]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Equipment not found']);
                }
            } catch (PDOException $e) {
                echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
            }
        } else {
            // Add new equipment or list all equipment
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                try {
                    $query = "INSERT INTO equipment (name, description, quantity, status) 
                             VALUES (:name, :description, :quantity, 'Available')";
                    
                    $stmt = $db->prepare($query);
                    $stmt->bindParam(':name', $_POST['name']);
                    $stmt->bindParam(':description', $_POST['description']);
                    $stmt->bindParam(':quantity', $_POST['quantity']);
                    
                    if ($stmt->execute()) {
                        echo json_encode(['success' => true, 'message' => 'Equipment added successfully']);
                    } else {
                        echo json_encode(['success' => false, 'message' => 'Failed to add equipment']);
                    }
                } catch (PDOException $e) {
                    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
                }
            } else {
                // List all equipment
                try {
                    $query = "SELECT * FROM equipment ORDER BY name";
                    $stmt = $db->query($query);
                    $equipment = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    echo json_encode(['success' => true, 'data' => $equipment]);
                } catch (PDOException $e) {
                    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
                }
            }
        }
        break;
}
?> 