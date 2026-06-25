<?php
// Home page: INDEX.html
session_start();
require_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

$is_admin = isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';

if($is_admin) {
    $query = "SELECT * FROM orders ORDER BY created_at DESC";
    $stmt = $db->prepare($query);
} else {
    $query = "SELECT * FROM orders WHERE user_id = :user_id ORDER BY created_at DESC";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':user_id', $_SESSION['user_id']);
}

$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(["success" => true, "orders" => $orders]);
?>


