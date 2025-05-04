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

try {
    $database = new Database();
    $db = $database->getConnection();

    // Handle different HTTP methods
    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            if (isset($_GET['id'])) {
                // Get single member
                $query = "SELECT m.*, p.name as plan_name 
                         FROM members m 
                         LEFT JOIN membership_plans p ON m.plan_id = p.id 
                         WHERE m.id = :id";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':id', $_GET['id']);
                $stmt->execute();
                
                if ($stmt->rowCount() > 0) {
                    $member = $stmt->fetch(PDO::FETCH_ASSOC);
                    echo json_encode([
                        'success' => true,
                        'member' => $member
                    ]);
                } else {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Member not found'
                    ]);
                }
            } else {
                // Get all members
                $query = "SELECT m.*, p.name as plan_name 
                         FROM members m 
                         LEFT JOIN membership_plans p ON m.plan_id = p.id 
                         ORDER BY m.id DESC";
                $stmt = $db->query($query);
                $members = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                echo json_encode([
                    'success' => true,
                    'members' => $members
                ]);
            }
            break;

        case 'POST':
            try {
                // Log the incoming data
                error_log("POST data received: " . print_r($_POST, true));

                if (isset($_POST['id'])) {
                    // Update member
                    $query = "UPDATE members SET 
                             name = :name,
                             email = :email,
                             phone = :phone,
                             plan_id = :plan_id,
                             status = :status
                             WHERE id = :id";
                    
                    $stmt = $db->prepare($query);
                    $stmt->bindParam(':id', $_POST['id']);
                    $stmt->bindParam(':name', $_POST['name']);
                    $stmt->bindParam(':email', $_POST['email']);
                    $stmt->bindParam(':phone', $_POST['phone']);
                    $stmt->bindParam(':plan_id', $_POST['plan_id']);
                    $stmt->bindParam(':status', $_POST['status']);
                    
                    if ($stmt->execute()) {
                        echo json_encode([
                            'success' => true,
                            'message' => 'Member updated successfully'
                        ]);
                    } else {
                        $error = $stmt->errorInfo();
                        throw new Exception('Failed to update member: ' . $error[2]);
                    }
                } else {
                    // Validate required fields
                    $required_fields = ['name', 'email', 'phone', 'plan_id', 'join_date'];
                    foreach ($required_fields as $field) {
                        if (!isset($_POST[$field]) || empty($_POST[$field])) {
                            throw new Exception("Missing required field: $field");
                        }
                    }

                    // Add new member
                    $query = "INSERT INTO members (name, email, phone, plan_id, join_date, expiry_date, status) 
                             VALUES (:name, :email, :phone, :plan_id, :join_date, 
                             DATE_ADD(:join_date2, INTERVAL (SELECT duration FROM membership_plans WHERE id = :plan_id2) MONTH),
                             'Active')";
                    
                    $stmt = $db->prepare($query);
                    $stmt->bindParam(':name', $_POST['name']);
                    $stmt->bindParam(':email', $_POST['email']);
                    $stmt->bindParam(':phone', $_POST['phone']);
                    $stmt->bindParam(':plan_id', $_POST['plan_id']);
                    $stmt->bindParam(':join_date', $_POST['join_date']);
                    $stmt->bindParam(':join_date2', $_POST['join_date']);
                    $stmt->bindParam(':plan_id2', $_POST['plan_id']);
                    
                    if ($stmt->execute()) {
                        echo json_encode([
                            'success' => true,
                            'message' => 'Member added successfully'
                        ]);
                    } else {
                        $error = $stmt->errorInfo();
                        throw new Exception('Failed to add member: ' . $error[2]);
                    }
                }
            } catch (PDOException $e) {
                error_log("Database Error: " . $e->getMessage());
                // Try to reconnect and retry once
                try {
                    $db = $database->getConnection();
                    // Retry the operation
                    if (isset($_POST['id'])) {
                        $stmt = $db->prepare($query);
                        $stmt->bindParam(':id', $_POST['id']);
                        $stmt->bindParam(':name', $_POST['name']);
                        $stmt->bindParam(':email', $_POST['email']);
                        $stmt->bindParam(':phone', $_POST['phone']);
                        $stmt->bindParam(':plan_id', $_POST['plan_id']);
                        $stmt->bindParam(':status', $_POST['status']);
                        
                        if ($stmt->execute()) {
                            echo json_encode([
                                'success' => true,
                                'message' => 'Member updated successfully'
                            ]);
                        } else {
                            throw new Exception('Failed to update member after retry');
                        }
                    } else {
                        $stmt = $db->prepare($query);
                        $stmt->bindParam(':name', $_POST['name']);
                        $stmt->bindParam(':email', $_POST['email']);
                        $stmt->bindParam(':phone', $_POST['phone']);
                        $stmt->bindParam(':plan_id', $_POST['plan_id']);
                        $stmt->bindParam(':join_date', $_POST['join_date']);
                        
                        if ($stmt->execute()) {
                            echo json_encode([
                                'success' => true,
                                'message' => 'Member added successfully'
                            ]);
                        } else {
                            throw new Exception('Failed to add member after retry');
                        }
                    }
                } catch (Exception $e2) {
                    error_log("Retry Error: " . $e2->getMessage());
                    echo json_encode([
                        'success' => false,
                        'message' => 'Database error: ' . $e2->getMessage()
                    ]);
                }
            } catch (Exception $e) {
                error_log("Error in members.php: " . $e->getMessage());
                echo json_encode([
                    'success' => false,
                    'message' => $e->getMessage()
                ]);
            }
            break;

        case 'DELETE':
            if (isset($_GET['id'])) {
                $query = "DELETE FROM members WHERE id = :id";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':id', $_GET['id']);
                
                if ($stmt->execute()) {
                    echo json_encode([
                        'success' => true,
                        'message' => 'Member deleted successfully'
                    ]);
                } else {
                    $error = $stmt->errorInfo();
                    echo json_encode([
                        'success' => false,
                        'message' => 'Failed to delete member: ' . $error[2]
                    ]);
                }
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Member ID is required'
                ]);
            }
            break;

        default:
            echo json_encode([
                'success' => false,
                'message' => 'Invalid request method'
            ]);
            break;
    }
} catch (Exception $e) {
    error_log("Fatal Error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'A system error occurred. Please try again.'
    ]);
}
?> 