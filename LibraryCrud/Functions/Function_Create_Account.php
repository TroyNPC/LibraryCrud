<?php
session_start();
$servername = "localhost";
$username_db = "root";
$password_db = "";   
$dbname = "booksdb";  

$conn = mysqli_connect($servername, $username_db, $password_db, $dbname);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if (isset($_POST['Register'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    $errors = [];
    if (empty($username)) {
        $errors[] = "Username is required.";
    }
    if (empty($password)) {
        $errors[] = "Password is required.";
    }
    if (empty($confirm_password)) {
        $errors[] = "Please confirm your password.";
    }
    if (strpos($password, ' ') !== false) {
        $errors[] = "Password cannot contain spaces.";
    }
    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    }
    if (empty($errors)) {
        $query = "SELECT * FROM users WHERE user_name = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
           echo "<script>alert('Username already exists. Please choose a different username.')</script>";
        } else {
            $query = "INSERT INTO users (user_name, password) VALUES (?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ss", $username, $password); 
            if ($stmt->execute()) {
                $_SESSION['message'] = "Registration successful!";
                header("Location: login.php");
                exit();
            } else {
               echo "<script>alert('Registration failed. Please try again.')</script>";
            }
        }
        $stmt->close();
    }

    if (!empty($errors)) {
        foreach ($errors as $error) {
            echo "<script>alert('$error');</script>";
        }
    }
}
?>
