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
    $required = ['order_number', 'customer_email', 'total_amount', 'payment_method', 'shipping_method', 'items'];
    foreach ($required as $field) {
        if (!isset($data[$field]) || empty($data[$field])) {
            throw new Exception("Missing required field: $field");
        }
    }
    
    $db = new Database();
    
    // Start transaction
    $db->beginTransaction();
    
    // Insert order
    $orderData = [
        'order_number' => $data['order_number'],
        'customer_email' => $data['customer_email'],
        'customer_name' => $data['customer_name'] ?? null,
        'customer_phone' => $data['customer_phone'] ?? null,
        'customer_address' => $data['customer_address'] ?? null,
        'total_amount' => $data['total_amount'],
        'discount_amount' => $data['discount_amount'] ?? 0,
        'shipping_amount' => $data['shipping_amount'] ?? 0,
        'payment_method' => $data['payment_method'],
        'shipping_method' => $data['shipping_method'],
        'status' => 'pending',
        'tracking_number' => $data['tracking_number'] ?? null,
        'notes' => $data['notes'] ?? null
    ];
    
    $orderId = $db->insert('orders', $orderData);
    
    // Insert order items
    foreach ($data['items'] as $item) {
        $itemData = [
            'order_id' => $orderId,
            'product_id' => $item['id'],
            'product_name' => $item['name'],
            'product_image' => $item['image_url'] ?? null,
            'quantity' => $item['quantity'],
            'unit_price' => $item['price'],
            'total_price' => $item['price'] * $item['quantity'],
            'product_description' => $item['description'] ?? null
        ];
        
        $db->insert('order_items', $itemData);
    }
    
    // Commit transaction
    $db->commit();
    
    // Send email notifications
    sendOrderEmails($data, $orderId);
    
    echo json_encode([
        'success' => true,
        'message' => 'Order saved successfully',
        'order_id' => $orderId
    ]);
    
} catch (Exception $e) {
    if (isset($db)) {
        $db->rollback();
    }
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

function sendOrderEmails($orderData, $orderId) {
    $adminEmail = 'homeofreta@gmail.com';
    $customerEmail = $orderData['customer_email'];
    
    // Admin email with enhanced details
    $adminSubject = "🛒 NEW ORDER #{$orderData['order_number']} - $" . number_format($orderData['total_amount'], 2);
    
    $adminBody = "NEW ORDER #{$orderData['order_number']}\n\n";
    $adminBody .= "CUSTOMER INFORMATION:\n";
    $adminBody .= "================\n";
    $adminBody .= "Name: " . ($orderData['customer_name'] ?? 'Not provided') . "\n";
    $adminBody .= "Email: $customerEmail\n";
    $adminBody .= "Phone: " . ($orderData['customer_phone'] ?? 'Not provided') . "\n";
    $adminBody .= "Address: " . ($orderData['customer_address'] ?? 'Not provided') . "\n\n";
    
    $adminBody .= "ORDER DETAILS:\n";
    $adminBody .= "==============\n";
    $itemCount = 1;
    foreach ($orderData['items'] as $item) {
        $adminBody .= "\n$itemCount. {$item['name']}\n";
        $adminBody .= "   Quantity: {$item['quantity']}\n";
        $adminBody .= "   Unit Price: $" . number_format($item['price'], 2) . "\n";
        $adminBody .= "   Total: $" . number_format($item['price'] * $item['quantity'], 2) . "\n";
        
        if (!empty($item['description'])) {
            $adminBody .= "   Description: " . $item['description'] . "\n";
        }
        
        if (!empty($item['image_url'])) {
            $adminBody .= "   Image: {$item['image_url']}\n";
            $adminBody .= "   Image URL: http://localhost/E-COMMECE/{$item['image_url']}\n";
        }
        
        $adminBody .= "   ----------------------------------------\n";
        $itemCount++;
    }
    
    $adminBody .= "\nORDER SUMMARY:\n";
    $adminBody .= "=============\n";
    $adminBody .= "Subtotal: $" . number_format($orderData['total_amount'], 2) . "\n";
    if (!empty($orderData['discount_amount'])) {
        $adminBody .= "Discount: -$" . number_format($orderData['discount_amount'], 2) . "\n";
    }
    $adminBody .= "Shipping: $" . number_format($orderData['shipping_amount'] ?? 0, 2) . "\n";
    $adminBody .= "TOTAL: $" . number_format($orderData['total_amount'], 2) . "\n\n";
    
    $adminBody .= "SHIPPING & PAYMENT:\n";
    $adminBody .= "==================\n";
    $adminBody .= "Payment Method: {$orderData['payment_method']}\n";
    $adminBody .= "Shipping Method: {$orderData['shipping_method']}\n";
    $adminBody .= "Tracking Number: " . ($orderData['tracking_number'] ?? 'To be assigned') . "\n\n";
    
    $adminBody .= "NEXT STEPS:\n";
    $adminBody .= "============\n";
    $adminBody .= "1. Contact customer to arrange payment\n";
    $adminBody .= "2. Process payment once confirmed\n";
    $adminBody .= "3. Prepare order for shipping\n";
    $adminBody .= "4. Update tracking number\n";
    
    // Customer email with enhanced details
    $customerSubject = "Order Confirmation #{$orderData['order_number']} - HOME OF RETA";
    $customerBody = "Thank you for your order from HOME OF RETA!\n\n";
    $customerBody .= "ORDER #{$orderData['order_number']}\n";
    $customerBody .= "========================\n\n";
    
    $customerBody .= "ORDER ITEMS:\n";
    $customerBody .= "============\n";
    $itemCount = 1;
    foreach ($orderData['items'] as $item) {
        $customerBody .= "\n$itemCount. {$item['name']}\n";
        $customerBody .= "   Quantity: {$item['quantity']}\n";
        $customerBody .= "   Price: $" . number_format($item['price'], 2) . "\n";
        $customerBody .= "   Total: $" . number_format($item['price'] * $item['quantity'], 2) . "\n";
        
        if (!empty($item['description'])) {
            $customerBody .= "   Details: " . $item['description'] . "\n";
        }
        
        $itemCount++;
    }
    
    $customerBody .= "\nORDER SUMMARY:\n";
    $customerBody .= "=============\n";
    $customerBody .= "Subtotal: $" . number_format($orderData['total_amount'], 2) . "\n";
    if (!empty($orderData['discount_amount'])) {
        $customerBody .= "Discount: -$" . number_format($orderData['discount_amount'], 2) . "\n";
    }
    $customerBody .= "Shipping: $" . number_format($orderData['shipping_amount'] ?? 0, 2) . "\n";
    $customerBody .= "TOTAL: $" . number_format($orderData['total_amount'], 2) . "\n\n";
    
    $customerBody .= "PAYMENT & SHIPPING:\n";
    $customerBody .= "==================\n";
    $customerBody .= "Payment Method: {$orderData['payment_method']}\n";
    $customerBody .= "Shipping Method: {$orderData['shipping_method']}\n";
    $customerBody .= "Tracking Number: " . ($orderData['tracking_number'] ?? 'Will be assigned soon') . "\n\n";
    
    $customerBody .= "WHAT HAPPENS NEXT?\n";
    $customerBody .= "==================\n";
    $customerBody .= "1. We will contact you shortly to arrange payment\n";
    $customerBody .= "2. Once payment is confirmed, we'll process your order\n";
    $customerBody .= "3. You'll receive tracking information when shipped\n";
    $customerBody .= "4. Expected delivery time depends on your chosen shipping method\n\n";
    
    $customerBody .= "CONTACT US:\n";
    $customerBody .= "===========\n";
    $customerBody .= "Email: $adminEmail\n";
    $customerBody .= "Phone: +653622655\n";
    $customerBody .= "Website: HOME OF RETA\n\n";
    
    $customerBody .= "Thank you for choosing HOME OF RETA! 🌍";
    
    // Send emails (you'll need to configure mail server)
    // mail($adminEmail, $adminSubject, $adminBody);
    // mail($customerEmail, $customerSubject, $customerBody);
    
    // For now, just log the emails with enhanced formatting
    error_log("=== ADMIN EMAIL ===\nSubject: " . $adminSubject . "\n\n" . $adminBody . "\n\n");
    error_log("=== CUSTOMER EMAIL ===\nSubject: " . $customerSubject . "\n\n" . $customerBody . "\n\n");
}
?>
