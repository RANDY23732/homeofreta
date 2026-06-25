<?php
// Home page: INDEX.html
session_start();
require_once '../config/database.php';

if(!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    echo json_encode(["success" => false, "message" => "Unauthorized"]);
    exit();
}

$database = new Database();
$db = $database->getConnection();

$data = json_decode(file_get_contents("php://input"));

$query = "UPDATE orders SET status = :status WHERE id = :id";
$stmt = $db->prepare($query);
$stmt->bindParam(':status', $data->status);
$stmt->bindParam(':id', $data->order_id);

if($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Order status updated"]);
} else {
    echo json_encode(["success" => false, "message" => "Failed to update order"]);
}
?>


