<?php
session_start();
require_once ' ./db.php';

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate input
    $pin = trim($_POST['pin']);
    $token = trim($_POST['token']);
    $new_password = trim($_POST['new_password']);
    $confirm_password = trim($_POST['confirm_password']);

    if (empty($new_password) || empty($confirm_password)) {
        $response['message'] = 'Both password fields are required.';
    } elseif ($new_password !== $confirm_password) {
        $response['message'] = 'Passwords do not match.';
    } else {
        // C onfirm if the pin and token match a record in the password_resets table
        $stmt = $db->prepare("SELECT email FROM password_resets WHERE pin = :pin AND token = :token LIMIT 1");
        $stmt->execute(['pin' => $pin, 'token' => $token]);
        $user = $stmt->fetch();

        if ($user) {
            $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
            $update_stmt = $db->prepare("UPDATE users SET password = :password WHERE email = :email");
            $update_stmt->execute(['password' => $hashed_password, 'email' => $user['email']]);
            
            // Delete the used token from password_resets
            $delete_stmt = $db->prepare("DELETE FROM password_resets WHERE pin = :pin AND token = :token");
            $delete_stmt->execute(['pin' => $pin, 'token' => $token]);

            $response['success'] = true;
            $response['message'] = 'Password updated successfully.';
        } else {
            $response['message'] = 'Invalid pin or token.';
        }
    }
}

echo json_encode($response);
?>