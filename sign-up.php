<?php
/*database connection*/
require_once ' ./db.php';

$response = ['success' => false, 'message' => '', 'field' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    /*Sanitize inputs*/
    $fullName = filter_var(trim($_POST['fullName']), FILTER_SANITIZE_STRING);
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $phone = filter_var(trim($_POST['phone']), FILTER_SANITIZE_STRING);
    $password = trim($_POST['password']);
    $confirmPassword = trim($_POST['confirmPassword']);

    /*Validate inputs*/
    if (empty($fullName)) {
        $response['message'] = 'Full Name is required.';
        $response['field'] = 'full-name';
    } elseif (empty($email)) {
        $response['message'] = 'Email is required.';
        $response['field'] = 'email';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response['message'] = 'Invalid email format.';
        $response['field'] = 'email';
    } elseif (empty($phone)) {
        $response['message'] = 'Phone number is required.';
        $response['field'] = 'phone';
    } elseif (!preg_match("/^[0-9]{10}$/", $phone)) {
        $response['message'] = 'Invalid phone number format.';
        $response['field'] = 'phone';
    } elseif (empty($password)) {
        $response['message'] = 'Password is required.';
        $response['field'] = 'password';
    } elseif (strlen($password) < 8) {
        $response['message'] = 'Password must be at least 8 characters long.';
        $response['field'] = 'password';
    } elseif ($password !== $confirmPassword) {
        $response['message'] = 'Passwords do not match.';
        $response['field'] = 'confirm-password';
    } else {
        $stmt = $db->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);

        if ($stmt->rowCount() > 0) {
            $response['message'] = 'Email already exists.';
            $response['field'] = 'email';
        } else {
            /*Insert user into the database*/
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
            $insertStmt = $db->prepare("INSERT INTO users (full_name, email, phone, password) VALUES (:fullName, :email, :phone, :password)");
            $insertStmt->bindParam(':fullName', $fullName);
            $insertStmt->bindParam(':email', $email);
            $insertStmt->bindParam(':phone', $phone);
            $insertStmt->bindParam(':password', $hashedPassword);
            $result = $insertStmt->execute();

            if ($result) {
                $response['success'] = true;
                $response['message'] = 'Registration successful!';
            } else {
                $response['message'] = 'Registration failed. Please try again.';
                error_log('Database insertion error: ' . implode(' | ', $insertStmt->errorInfo()));
            }
        }
    }
}

echo json_encode($response);
?>