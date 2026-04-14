<?php

require_once ' ./db.php';
require_once __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$response = ['success' => false, 'message' => ''];

// Sanitize and validate email input
$email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);

if (!$email) {
    $response['message'] = "Please enter a valid email address.";
    echo json_encode($response);
    exit;
}

try {
    // Check if email exists in the users table
    $stmt = $db->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch();

    if (!$user) {
        $response['message'] = "No account found with that email.";
        echo json_encode($response);
        exit;
    }

// Generate pin and token
    $token = bin2hex(random_bytes(16));
    $pin = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

    // Store the pin and token in the password_resets table
    $stmt = $db->prepare("INSERT INTO password_resets (email, token, pin, expiration_time) 
                          VALUES (:email, :token, :pin, DATE_ADD(NOW(), INTERVAL 1 HOUR))");
    $stmt->execute([':email' => $email, ':token' => $token, ':pin' => $pin]);

    // Set password reset link
    $resetLink = "http://localhost/final-project/set-password.php?pin=" . urlencode($pin) . "&token=" . urlencode($token);

    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = getenv('SMTP_USERNAME');
    $mail->Password = getenv('SMTP_PASSWORD'); 
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    $mail->setFrom('no-reply@yourdomain.com', 'Work It');
    $mail->addAddress($email);

    $mail->isHTML(true);
    $mail->Subject = 'Password Reset Request';
    $mail->Body = "<p>We received a request to reset your password. Use the following link to reset it:</p>
                   <p><a href='$resetLink'>Reset Password</a></p>
                   <p>If you did not request a password reset, please ignore this email.</p>";

    // Send email and handle result
    if ($mail->send()) {
        $response['success'] = true;
        $response['message'] = "Password reset email sent successfully.";
    } else {
        $response['message'] = "Failed to send reset email. Please check SMTP settings.";
    }
} catch (Exception $e) {
    $response['message'] = "An error occurred: " . $e->getMessage();
}

// Output JSON response
header('Content-Type: application/json');
echo json_encode($response);
?>