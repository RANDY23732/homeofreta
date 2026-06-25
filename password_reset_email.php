<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once __DIR__ . '/mail_helper.php';

const CODE_TTL_SECONDS = 900; // 15 minutes
const RESEND_COOLDOWN_SECONDS = 60;

$dataDir = __DIR__ . '/data';
$storeFile = $dataDir . '/password_reset_codes.json';

function respond($payload, $status = 200) {
    http_response_code($status);
    echo json_encode($payload);
    exit;
}

function load_store($file) {
    if (!file_exists($file)) {
        return [];
    }
    $raw = file_get_contents($file);
    $decoded = json_decode($raw, true);
    return is_array($decoded) ? $decoded : [];
}

function save_store($file, $data) {
    file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT), LOCK_EX);
}

function is_gmail_address($email) {
    $email = strtolower(trim($email));
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return false;
    }
    if (!preg_match('/^[a-z0-9](?:[a-z0-9.+_-]*[a-z0-9])?@gmail\.com$/', $email)) {
        return false;
    }
    return true;
}

$payload = json_decode(file_get_contents('php://input'), true);
if (!is_array($payload) || empty($payload['action'])) {
    respond(['success' => false, 'message' => 'Invalid request'], 400);
}

if (!is_dir($dataDir)) {
    mkdir($dataDir, 0755, true);
}

$action = $payload['action'];
$email = isset($payload['email']) ? strtolower(trim($payload['email'])) : '';

if ($action === 'send') {
    if (!is_gmail_address($email)) {
        respond(['success' => false, 'message' => 'Please use a valid @gmail.com address.'], 400);
    }

    $store = load_store($storeFile);
    $now = time();
    foreach ($store as $key => $entry) {
        if (($entry['expires_at'] ?? 0) < $now) {
            unset($store[$key]);
        }
    }

    if (isset($store[$email])) {
        $lastSent = (int)($store[$email]['sent_at'] ?? 0);
        if ($now - $lastSent < RESEND_COOLDOWN_SECONDS) {
            $wait = RESEND_COOLDOWN_SECONDS - ($now - $lastSent);
            respond(['success' => false, 'message' => "Please wait {$wait}s before requesting another code."], 429);
        }
    }

    // Generate unique 6-digit code
    $code = (string) random_int(100000, 999999);
    $subject = 'HOME OF RETA — Password Reset Code';
    $body = "Your password reset code is: {$code}\n\n";
    $body .= "This code expires in 15 minutes.\n";
    $body .= "If you did not request this password reset, ignore this email.\n\n";
    $body .= "— HOME OF RETA 🌏\n";

    $mailResult = send_app_mail($email, $subject, $body);
    if (!$mailResult['ok']) {
        respond(['success' => false, 'message' => $mailResult['error'] ?? 'Failed to send password reset email.'], 500);
    }

    $store[$email] = [
        'code_hash' => password_hash($code, PASSWORD_DEFAULT),
        'expires_at' => $now + CODE_TTL_SECONDS,
        'sent_at' => $now,
        'attempts' => 0
    ];
    save_store($storeFile, $store);

    $response = [
        'success' => true,
        'message' => 'Password reset code sent to your Gmail inbox. Check spam if you do not see it.',
        'expires_in' => CODE_TTL_SECONDS
    ];
    if (($mailResult['method'] ?? '') === 'local_dev') {
        error_log("HOME OF RETA password reset for {$email}: {$code}");
        $response['dev_hint'] = 'Local mode: check Apache error log for the code if mail is not configured.';
    }
    respond($response);
}

if ($action === 'verify') {
    $code = isset($payload['code']) ? trim((string) $payload['code']) : '';
    if (!is_gmail_address($email) || !preg_match('/^\d{6}$/', $code)) {
        respond(['success' => false, 'message' => 'Enter your Gmail address and the 6-digit code from your inbox.'], 400);
    }

    $store = load_store($storeFile);
    if (!isset($store[$email])) {
        respond(['success' => false, 'message' => 'No reset code found. Request a new code.'], 404);
    }

    $entry = $store[$email];
    if (time() > (int)($entry['expires_at'] ?? 0)) {
        unset($store[$email]);
        save_store($storeFile, $store);
        respond(['success' => false, 'message' => 'Code expired. Request a new password reset code.'], 410);
    }

    $entry['attempts'] = (int)($entry['attempts'] ?? 0) + 1;
    if ($entry['attempts'] > 8) {
        unset($store[$email]);
        save_store($storeFile, $store);
        respond(['success' => false, 'message' => 'Too many attempts. Request a new code.'], 429);
    }

    if (!password_verify($code, $entry['code_hash'])) {
        $store[$email] = $entry;
        save_store($storeFile, $store);
        respond(['success' => false, 'message' => 'Incorrect code. Check your Gmail inbox and try again.'], 401);
    }

    unset($store[$email]);
    save_store($storeFile, $store);
    respond(['success' => true, 'message' => 'Code verified successfully. You can now reset your password.', 'verified' => true]);
}

respond(['success' => false, 'message' => 'Unknown action'], 400);
?>
