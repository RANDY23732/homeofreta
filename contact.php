<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once 'Database.php';

try {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!$data) {
        throw new Exception('Invalid JSON data');
    }
    
    // Validate required fields
    $required = ['name', 'email', 'subject', 'message'];
    foreach ($required as $field) {
        if (!isset($data[$field]) || empty(trim($data[$field]))) {
            throw new Exception("Missing required field: $field");
        }
    }
    
    // Validate email
    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Invalid email address');
    }
    
    $db = new Database();
    
    // Insert contact message
    $messageData = [
        'name' => trim($data['name']),
        'email' => trim($data['email']),
        'subject' => trim($data['subject']),
        'message' => trim($data['message'])
    ];
    
    $messageId = $db->insert('contact_messages', $messageData);
    
    // Send email notification to admin
    $adminEmail = 'homeofreta@gmail.com';
    $subject = "New Contact Message: {$data['subject']}";
    $body = "You have received a new contact message:\n\n";
    $body .= "From: {$data['name']} <{$data['email']}>\n";
    $body .= "Subject: {$data['subject']}\n\n";
    $body .= "Message:\n{$data['message']}\n\n";
    $body .= "Time: " . date('Y-m-d H:i:s');
    
    // Send email (you'll need to configure mail server)
    // mail($adminEmail, $subject, $body);
    
    // For now, just log the message
    error_log("Contact Message: " . $subject . "\n" . $body);
    
    echo json_encode([
        'success' => true,
        'message' => 'Contact message sent successfully',
        'message_id' => $messageId
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
