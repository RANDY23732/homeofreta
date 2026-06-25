<?php
// Home page: INDEX.html
session_start();
require_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

$data = json_decode(file_get_contents("php://input"));

$user_id = $_SESSION['user_id'] ?? null;
$session_id = session_id();

// Check if product already in cart
$check_query = "SELECT id, quantity FROM cart WHERE product_id = :product_id AND (" . 
               ($user_id ? "user_id = :user_id" : "session_id = :session_id") . ")";
$check_stmt = $db->prepare($check_query);
$check_stmt->bindParam(':product_id', $data->product_id);
if($user_id) {
    $check_stmt->bindParam(':user_id', $user_id);
} else {
    $check_stmt->bindParam(':session_id', $session_id);
}
$check_stmt->execute();

if($check_stmt->rowCount() > 0) {
    $existing = $check_stmt->fetch(PDO::FETCH_ASSOC);
    $new_qty = $existing['quantity'] + $data->quantity;
    $update_query = "UPDATE cart SET quantity = :quantity WHERE id = :id";
    $update_stmt = $db->prepare($update_query);
    $update_stmt->bindParam(':quantity', $new_qty);
    $update_stmt->bindParam(':id', $existing['id']);
    $update_stmt->execute();
} else {
    $insert_query = "INSERT INTO cart (user_id, session_id, product_id, product_name, product_price, quantity) 
                     VALUES (:user_id, :session_id, :product_id, :product_name, :product_price, :quantity)";
    $insert_stmt = $db->prepare($insert_query);
    $insert_stmt->bindParam(':user_id', $user_id);
    $insert_stmt->bindParam(':session_id', $session_id);
    $insert_stmt->bindParam(':product_id', $data->product_id);
    $insert_stmt->bindParam(':product_name', $data->product_name);
    $insert_stmt->bindParam(':product_price', $data->product_price);
    $insert_stmt->bindParam(':quantity', $data->quantity);
    $insert_stmt->execute();
}

echo json_encode(["success" => true, "message" => "Added to cart"]);
?>


