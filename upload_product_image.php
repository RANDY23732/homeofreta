<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

if(empty($_FILES['image'])) {
    echo json_encode(["success" => false, "message" => "No image uploaded", "debug" => "FILES: " . print_r($_FILES, true)]);
    exit();
}

$uploadDir = __DIR__ . '/uploads';
if(!is_dir($uploadDir)) {
    if(!mkdir($uploadDir, 0755, true)) {
        echo json_encode(["success" => false, "message" => "Failed to create uploads directory"]);
        exit();
    }
}

$image = $_FILES['image'];

// Check for upload errors
if($image['error'] !== UPLOAD_ERR_OK) {
    $errorMessages = [
        UPLOAD_ERR_INI_SIZE => 'The uploaded file exceeds the upload_max_filesize directive in php.ini',
        UPLOAD_ERR_FORM_SIZE => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form',
        UPLOAD_ERR_PARTIAL => 'The uploaded file was only partially uploaded',
        UPLOAD_ERR_NO_FILE => 'No file was uploaded',
        UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder',
        UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
        UPLOAD_ERR_EXTENSION => 'A PHP extension stopped the file upload',
    ];
    
    $errorMsg = $errorMessages[$image['error']] ?? 'Unknown upload error';
    echo json_encode(["success" => false, "message" => $errorMsg, "error_code" => $image['error']]);
    exit();
}

$extension = strtolower(pathinfo($image['name'], PATHINFO_EXTENSION));
$allowed = ['jpg','jpeg','png','gif','webp','jfif'];
if(!in_array($extension, $allowed)) {
    echo json_encode(["success" => false, "message" => "Invalid image type. Allowed: " . implode(', ', $allowed), "extension" => $extension]);
    exit();
}

// Check file size (max 5MB)
if($image['size'] > 5 * 1024 * 1024) {
    echo json_encode(["success" => false, "message" => "Image too large. Max size is 5MB", "size" => $image['size']]);
    exit();
}

// Check if it's actually an image
if(!getimagesize($image['tmp_name'])) {
    echo json_encode(["success" => false, "message" => "File is not a valid image"]);
    exit();
}

$filename = uniqid('product_') . '.' . $extension;
$targetPath = $uploadDir . '/' . $filename;

// Debug information
$debugInfo = [
    'upload_dir' => $uploadDir,
    'target_path' => $targetPath,
    'tmp_name' => $image['tmp_name'],
    'file_exists' => file_exists($image['tmp_name']),
    'is_writable' => is_writable($uploadDir)
];

if(move_uploaded_file($image['tmp_name'], $targetPath)) {
    // Verify the file was actually moved
    if(file_exists($targetPath)) {
        echo json_encode([
            "success" => true, 
            "message" => "Image uploaded successfully", 
            "image_url" => $filename,
            "full_path" => $targetPath,
            "debug" => $debugInfo
        ]);
    } else {
        echo json_encode(["success" => false, "message" => "File move reported success but file not found", "debug" => $debugInfo]);
    }
} else {
    echo json_encode([
        "success" => false, 
        "message" => "Failed to move uploaded image", 
        "debug" => $debugInfo,
        "upload_error" => $image['error']
    ]);
}
?>