<?php
// Home page: INDEX.html
session_start();
require_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

$data = json_decode(file_get_contents("php://input"));

if(!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "Please login first"]);
    exit();
}

$order_number = 'RETA' . time() . rand(100, 999);
$tracking_number = 'TRK' . strtoupper(uniqid());

$itemsJson = json_encode(array_map(function($item){
    return [
        'product_id' => $item->product_id ?? null,
        'name' => $item->name ?? '',
        'qty' => $item->qty ?? 1,
        'price' => $item->price ?? 0,
        'description' => $item->description ?? '',
        'image_url' => $item->image_url ?? ''
    ];
}, $data->items));

$query = "INSERT INTO orders (order_number, user_id, user_email, customer_name, total, items, payment_method, shipping_method, customer_address, customer_phone, tracking_number) 
          VALUES (:order_number, :user_id, :user_email, :customer_name, :total, :items, :payment_method, :shipping_method, :customer_address, :customer_phone, :tracking_number)";

$stmt = $db->prepare($query);
$stmt->bindParam(':order_number', $order_number);
$stmt->bindParam(':user_id', $_SESSION['user_id']);
$stmt->bindParam(':user_email', $data->user_email);
$stmt->bindParam(':customer_name', $data->customer_name);
$stmt->bindParam(':total', $data->total);
$stmt->bindParam(':items', $itemsJson);
$stmt->bindParam(':payment_method', $data->payment_method);
$stmt->bindParam(':shipping_method', $data->shipping_method);
$stmt->bindParam(':customer_address', $data->customer_address);
$stmt->bindParam(':customer_phone', $data->customer_phone);
$stmt->bindParam(':tracking_number', $tracking_number);

if($stmt->execute()) {
    echo json_encode([
        "success" => true, 
        "message" => "Order created", 
        "order_number" => $order_number,
        "tracking_number" => $tracking_number
    ]);
} else {
    echo json_encode(["success" => false, "message" => "Failed to create order"]);
}
?>


