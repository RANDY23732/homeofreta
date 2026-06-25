<?php
// Home page: INDEX.html
session_start();
require_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

$data = json_decode(file_get_contents("php://input"));

// Admin credentials
define('ADMIN_EMAIL', 'homeofreta@gmail.com');
define('ADMIN_PHONE', '653622655');

if(isset($data->email) && isset($data->password)) {
    $query = "SELECT * FROM users WHERE email = :email";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':email', $data->email);
    $stmt->execute();
    
    if($stmt->rowCount() > 0) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Check if this is admin login
        if(strtolower($user['email']) === strtolower(ADMIN_EMAIL)) {
            // Admin authentication requires phone verification
            if(!isset($data->phone)) {
                echo json_encode(["success" => false, "message" => "Admin phone verification required"]);
                exit;
            }
            
            if($data->phone !== ADMIN_PHONE) {
                echo json_encode(["success" => false, "message" => "Invalid admin phone number"]);
                exit;
            }
        }
        
        // Verify password
        if(password_verify($data->password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_uid'] = $user['uid'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];
            
            echo json_encode([
                "success" => true,
                "message" => "Login successful",
                "user" => [
                    "uid" => $user['uid'],
                    "email" => $user['email'],
                    "fullname" => $user['fullname'],
                    "phone" => $user['phone'],
                    "address" => $user['address'],
                    "role" => $user['role']
                ]
            ]);
        } else {
            echo json_encode(["success" => false, "message" => "Invalid password"]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "User not found"]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Email and password required"]);
}
?>


