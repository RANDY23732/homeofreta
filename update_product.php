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

$query = "UPDATE products SET 
          name = :name,
          category = :category,
          price = :price,
          old_price = :old_price,
          stock = :stock,
          rating = :rating,
          description = :description,
          benefits = :benefits,
          usage_instructions = :usage_instructions,
          image_url = :image_url,
          bestseller = :bestseller
          WHERE id = :id";

$stmt = $db->prepare($query);
$stmt->bindParam(':name', $data->name);
$stmt->bindParam(':category', $data->category);
$stmt->bindParam(':price', $data->price);
$stmt->bindParam(':old_price', $data->oldPrice);
$stmt->bindParam(':stock', $data->stock);
$stmt->bindParam(':rating', $data->rating);
$stmt->bindParam(':description', $data->desc);
$stmt->bindParam(':benefits', $data->benefits);
$stmt->bindParam(':usage_instructions', $data->usage);
$stmt->bindParam(':image_url', $data->image);
$stmt->bindParam(':bestseller', $data->bestseller);
$stmt->bindParam(':id', $data->id);

if($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Product updated successfully"]);
} else {
    echo json_encode(["success" => false, "message" => "Failed to update product"]);
}
?>


