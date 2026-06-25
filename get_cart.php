<?php
// Home page: INDEX.html
session_start();
require_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

$user_id = $_SESSION['user_id'] ?? null;

if($user_id) {
    $query = "SELECT * FROM cart WHERE user_id = :user_id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':user_id', $user_id);
} else {
    $session_id = session_id();
    $query = "SELECT * FROM cart WHERE session_id = :session_id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':session_id', $session_id);
}

$stmt->execute();
$cart = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(["success" => true, "cart" => $cart]);
?>


