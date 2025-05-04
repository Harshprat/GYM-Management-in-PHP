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
        // Update trainer
        if (isset($_POST['id'])) {
            try {
                $query = "UPDATE trainers SET 
                    name = :name,
                    email = :email,
                    phone = :phone,
                    specialization = :specialization,
                    status = :status
                    WHERE id = :id";
                
                $stmt = $db->prepare($query);
                $stmt->bindParam(':id', $_POST['id']);
                $stmt->bindParam(':name', $_POST['name']);
                $stmt->bindParam(':email', $_POST['email']);
                $stmt->bindParam(':phone', $_POST['phone']);
                $stmt->bindParam(':specialization', $_POST['specialization']);
                $stmt->bindParam(':status', $_POST['status']);
                
                if ($stmt->execute()) {
                    echo json_encode(['success' => true, 'message' => 'Trainer updated successfully']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Failed to update trainer']);
                }
            } catch (PDOException $e) {
                echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
            }
        }
        break;

    case 'delete':
        // Delete trainer
        if (isset($_POST['id'])) {
            try {
                $query = "DELETE FROM trainers WHERE id = :id";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':id', $_POST['id']);
                
                if ($stmt->execute()) {
                    echo json_encode(['success' => true, 'message' => 'Trainer deleted successfully']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Failed to delete trainer']);
                }
            } catch (PDOException $e) {
                echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
            }
        }
        break;

    default:
        // Get single trainer or list all trainers
        if (isset($_GET['id'])) {
            try {
                $query = "SELECT * FROM trainers WHERE id = :id";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':id', $_GET['id']);
                $stmt->execute();
                
                if ($stmt->rowCount() > 0) {
                    $trainer = $stmt->fetch(PDO::FETCH_ASSOC);
                    echo json_encode(['success' => true, 'data' => $trainer]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Trainer not found']);
                }
            } catch (PDOException $e) {
                echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
            }
        } else {
            // Add new trainer or list all trainers
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                try {
                    $query = "INSERT INTO trainers (name, email, phone, specialization) 
                             VALUES (:name, :email, :phone, :specialization)";
                    
                    $stmt = $db->prepare($query);
                    $stmt->bindParam(':name', $_POST['name']);
                    $stmt->bindParam(':email', $_POST['email']);
                    $stmt->bindParam(':phone', $_POST['phone']);
                    $stmt->bindParam(':specialization', $_POST['specialization']);
                    
                    if ($stmt->execute()) {
                        echo json_encode(['success' => true, 'message' => 'Trainer added successfully']);
                    } else {
                        echo json_encode(['success' => false, 'message' => 'Failed to add trainer']);
                    }
                } catch (PDOException $e) {
                    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
                }
            } else {
                // List all trainers
                try {
                    $query = "SELECT * FROM trainers ORDER BY name";
                    $stmt = $db->query($query);
                    $trainers = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    echo json_encode(['success' => true, 'data' => $trainers]);
                } catch (PDOException $e) {
                    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
                }
            }
        }
        break;
}
?> 