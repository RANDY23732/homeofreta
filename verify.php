<?php
// Home page: INDEX.html
session_start();
if(isset($_SESSION['user_id'])) {
    echo json_encode([
        "authenticated" => true,
        "user" => [
            "uid" => $_SESSION['user_uid'],
            "email" => $_SESSION['user_email'],
            "role" => $_SESSION['user_role']
        ]
    ]);
} else {
    echo json_encode(["authenticated" => false]);
}
?>


