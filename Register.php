<?php
// Home page: INDEX.html
require_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

$data = json_decode(file_get_contents("php://input"));

if(isset($data->email) && isset($data->password) && isset($data->fullname)) {
    
    $check_query = "SELECT id FROM users WHERE email = :email";
    $check_stmt = $db->prepare($check_query);
    $check_stmt->bindParam(':email', $data->email);
    $check_stmt->execute();
    
    if($check_stmt->rowCount() > 0) {
        echo json_encode(["success" => false, "message" => "Email already exists"]);
        exit();
    }
    
    $uid = uniqid('user_');
    $hashed_password = password_hash($data->password, PASSWORD_DEFAULT);
    
    $query = "INSERT INTO users (uid, email, fullname, phone, password, role) 
              VALUES (:uid, :email, :fullname, :phone, :password, 'user')";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':uid', $uid);
    $stmt->bindParam(':email', $data->email);
    $stmt->bindParam(':fullname', $data->fullname);
    $stmt->bindParam(':phone', $data->phone);
    $stmt->bindParam(':password', $hashed_password);
    
    if($stmt->execute()) {
        echo json_encode([
            "success" => true, 
            "message" => "Registration successful",
            "user" => [
                "uid" => $uid,
                "email" => $data->email,
                "fullname" => $data->fullname,
                "role" => "user"
            ]
        ]);
    } else {
        echo json_encode(["success" => false, "message" => "Registration failed"]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Missing required fields"]);
}
?>


