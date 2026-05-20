<?php
// Start the session (if required for user tracking)
session_start();
echo json_encode(['success' => true, 'redirect' => 'dashboard.php']);

// Function to sanitize user input
function sanitize($data) {
    return htmlspecialchars(trim($data));
}

// Connect to the database (adjust credentials accordingly)
require_once('./db.php'); 

$message = "PHP loaded Sign-in.php";
echo "<script type='text/javascript'>alert('$message');</script>";


try {
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . $e->getMessage()]);
    exit;
}

// Check if POST data is set
if (isset($_POST['username']) && isset($_POST['password'])) {
    // Sanitize user input
    $username = sanitize($_POST['username']);
    $password = sanitize($_POST['password']);

    // Validate input
    if (empty($username) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'Both fields are required.']);
        exit;
    }

    // Prepare SQL to check if the user exists
    $stmt = $pdo->prepare('SELECT password FROM users WHERE username = :username');
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Check if the password matches
        if (password_verify($password, $user['password'])) {
            // Password matches, sign-in successful
            $_SESSION['username'] = $username;  // Set session variable (if needed)
            echo json_encode(['success' => true, 'redirect' => 'dashboard.php']);
        } else {
            // Password does not match
            echo json_encode(['success' => false, 'message' => 'Invalid username or password.']);
        }
    } else {
        // Username does not exist
        echo json_encode(['success' => false, 'message' => 'Invalid username or password.']);
    }
} else {
    // Missing username or password
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}

?>
