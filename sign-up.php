<?php
// Include necessary files for database and email (PHPMailer)

require_once('./db.php');  // Your database connection file

// require_once('../vendor/autoload.php');  // If using PHPMailer via Composer

$message = "PHP loaded";
echo "<script type='text/javascript'>alert('$message');</script>";

// Define variables and initialize with empty values
$name = $email = $password = "";
$name_err = $email_err = $password_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Sanitize inputs
    $name = filter_var(trim($_POST['name']), FILTER_SANITIZE_STRING);
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password = trim($_POST['password']);

    // Validate inputs
    if (empty($name)) {
        $name_err = "Name is required.";
    }
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $email_err = "A valid email is required.";
    } else {
        // Check for duplicate email in database
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            $email_err = "This email is already registered.";
        }
    }
    if (empty($password) || strlen($password) < 6) {
        $password_err = "Password must be at least 6 characters long.";
    }

    // If there are errors, send response back to the AJAX call
    if (!empty($name_err) || !empty($email_err) || !empty($password_err)) {
        echo json_encode([
            'success' => false,
            'message' => "Please fix the errors below.",
            'errors' => [
                'name' => $name_err,
                'email' => $email_err,
                'password' => $password_err,
            ]
        ]);
        exit;
    }

    // Hash the password before inserting into the database
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert data into the database
    $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (:username, :email, :password)");
    $stmt->bindParam(':username', $name);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password', $hashed_password);
    
    if ($stmt->execute()) {
        // Send confirmation email
        /*
        $mail = new PHPMailer\PHPMailer\PHPMailer();
        $mail->isSMTP();
        $mail->Host = 'smtp.example.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'your-email@example.com';
        $mail->Password = 'your-email-password';
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('no-reply@example.com', 'Your Website');
        $mail->addAddress($email, $name);
        $mail->isHTML(true);
        $mail->Subject = 'Welcome to Our Website';
        $mail->Body    = "<h1>Welcome, $name!</h1><p>Thank you for signing up with us.</p>";
        */
        {
            // Send success response
            echo json_encode([
                'success' => true,
                'message' => "Sign up successful! A confirmation email has been sent.",
            ]);
        } 
        /* else {
            echo json_encode([
                'success' => false,
                'message' => "Sign up failed, could not send email.",
            ]);
        }
    } else {
        echo json_encode([
            'success' => false,
            'message' => "Something went wrong. Please try again.",
        ]); */
    } 
}
?>
