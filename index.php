<?php
// Database connection
$host = "localhost";
$user = "root";
$password = "";
$dbname = "auth_db";

$conn = new mysqli($host, $user, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// SIGN UP
if (isset($_POST['signup'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $sql = "INSERT INTO users (username, email, password) 
            VALUES ('$username', '$email', '$password')";

    if ($conn->query($sql) === TRUE) {
        echo "Signup successful!";
    } else {
        echo "Error: " . $conn->error;
    }
}

// SIGN IN
if (isset($_POST['signin'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email='$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        if (password_verify($password, $row['password'])) {
            echo "Login successful! Welcome " . $row['username'];
        } else {
            echo "Incorrect password!";
        }
    } else {
        echo "User not found!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Auth Page</title>
    <style>
        body { font-family: Arial; }
        .container { display: flex; gap: 50px; }
        form { border: 1px solid #ccc; padding: 20px; width: 250px; }
        input { display: block; margin-bottom: 10px; width: 100%; padding: 8px; }
        button { padding: 8px; width: 100%; }
    </style>
</head>
<body>

<h2>PHP Login & Signup</h2>

<div class="container">
    <!-- SIGN UP -->
    <form method="POST">
        <h3>Sign Up</h3>
        <input type="text" name="username" placeholder="Username" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit" name="signup">Sign Up</button>
    </form>

    <!-- SIGN IN -->
    <form method="POST">
        <h3>Sign In</h3>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit" name="signin">Sign In</button>
    </form>
</div>

</body>
</html>