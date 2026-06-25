<?php
require_once __DIR__ . '/email_config.php';

function send_app_mail($to, $subject, $body) {
    $to = trim($to);
    if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
        return ['ok' => false, 'error' => 'Invalid recipient'];
    }

    if (SMTP_PASS !== '') {
        $smtp = send_smtp_mail($to, $subject, $body);
        if ($smtp['ok']) {
            return $smtp;
        }
    }

    $headers = [
        'MIME-Version: 1.0',
        'Content-type: text/plain; charset=UTF-8',
        'From: ' . SMTP_FROM_NAME . ' <' . SMTP_FROM . '>',
        'Reply-To: ' . SMTP_FROM,
        'X-Mailer: PHP/' . phpversion()
    ];
    $sent = @mail($to, $subject, $body, implode("\r\n", $headers));
    if ($sent) {
        return ['ok' => true, 'method' => 'mail'];
    }

    $host = $_SERVER['HTTP_HOST'] ?? '';
    $isLocal = preg_match('/^(localhost|127\.0\.0\.1)(:\d+)?$/i', $host);
    if ($isLocal) {
        return ['ok' => true, 'method' => 'local_dev', 'dev_note' => 'SMTP not configured; code logged locally.'];
    }

    return ['ok' => false, 'error' => 'Could not send email. Set SMTP_PASS in email_config.php with a Gmail App Password.'];
}

function send_smtp_mail($to, $subject, $body) {
    $errno = 0;
    $errstr = '';
    $socket = @stream_socket_client(
        'tcp://' . SMTP_HOST . ':' . SMTP_PORT,
        $errno,
        $errstr,
        15,
        STREAM_CLIENT_CONNECT
    );
    if (!$socket) {
        return ['ok' => false, 'error' => "SMTP connect failed: $errstr"];
    }

    stream_set_timeout($socket, 15);
    $read = function () use ($socket) {
        $data = '';
        while ($line = fgets($socket, 515)) {
            $data .= $line;
            if (isset($line[3]) && $line[3] === ' ') {
                break;
            }
        }
        return $data;
    };
    $write = function ($cmd) use ($socket, $read) {
        fwrite($socket, $cmd . "\r\n");
        return $read();
    };

    $read();
    $write('EHLO localhost');
    fwrite($socket, "STARTTLS\r\n");
    $read();
    if (!stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
        fclose($socket);
        return ['ok' => false, 'error' => 'STARTTLS failed'];
    }
    $write('EHLO localhost');
    $write('AUTH LOGIN');
    $write(base64_encode(SMTP_USER));
    $auth = $write(base64_encode(SMTP_PASS));
    if (strpos($auth, '235') === false) {
        fclose($socket);
        return ['ok' => false, 'error' => 'SMTP authentication failed'];
    }
    $write('MAIL FROM:<' . SMTP_FROM . '>');
    $write('RCPT TO:<' . $to . '>');
    $write('DATA');
    $message = "From: " . SMTP_FROM_NAME . " <" . SMTP_FROM . ">\r\n";
    $message .= "To: <{$to}>\r\n";
    $message .= "Subject: {$subject}\r\n";
    $message .= "MIME-Version: 1.0\r\n";
    $message .= "Content-Type: text/plain; charset=UTF-8\r\n\r\n";
    $message .= $body . "\r\n.";
    $write($message);
    $write('QUIT');
    fclose($socket);
    return ['ok' => true, 'method' => 'smtp'];
}
