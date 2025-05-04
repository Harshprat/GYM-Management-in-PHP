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

try {
    // Get total members
    $query = "SELECT COUNT(*) as total FROM members";
    $stmt = $db->query($query);
    $totalMembers = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Get total trainers (only active)
    $query = "SELECT COUNT(*) as total FROM trainers WHERE status = 'Active'";
    $stmt = $db->query($query);
    $totalTrainers = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Get total equipment
    $query = "SELECT COUNT(*) as total FROM equipment";
    $stmt = $db->query($query);
    $totalEquipment = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Get total plans
    $query = "SELECT COUNT(*) as total FROM membership_plans";
    $stmt = $db->query($query);
    $totalPlans = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Get recent members
    $query = "SELECT name, join_date, status FROM members ORDER BY join_date DESC LIMIT 5";
    $stmt = $db->query($query);
    $recentMembers = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get upcoming renewals
    $query = "SELECT m.name as member_name, p.name as plan_name, m.expiry_date 
              FROM members m 
              JOIN membership_plans p ON m.plan_id = p.id 
              WHERE m.expiry_date >= CURDATE() 
              ORDER BY m.expiry_date ASC 
              LIMIT 5";
    $stmt = $db->query($query);
    $upcomingRenewals = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'totalMembers' => $totalMembers,
        'totalTrainers' => $totalTrainers,
        'totalEquipment' => $totalEquipment,
        'totalPlans' => $totalPlans,
        'recentMembers' => $recentMembers,
        'upcomingRenewals' => $upcomingRenewals
    ]);

} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?> 