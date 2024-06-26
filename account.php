<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);


if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

require 'config/db.php';

$userId = $_SESSION['user_id'] ?? null;
$isAdmin = $_SESSION['is_admin'] ?? 0; 

if ($userId) {
    $stmt = $conn->prepare("SELECT username, email, is_admin FROM users WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            $username = $user['username'];
            $email = $user['email'];
            $isAdmin = $user['is_admin']; 
            $_SESSION['is_admin'] = $isAdmin;
        } else {
            $username = 'User not found';
            $email = 'No email set';
        }
        $stmt->close();
    } else {
        echo 'SQL Error: ' . $conn->error;
        exit;
    }
} else {
    header('Location: login.php');
    exit;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Account</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Welcome, <?= htmlspecialchars($username); ?>!</h1>
        <p>Your email address is: <?= htmlspecialchars($email); ?></p>
        <a href="config/logout.php" class="btn btn-primary">Logout</a>
        <?php if ($isAdmin): ?>
            <a href="admin/index.php" class="btn btn-secondary mt-3">Admin Dashboard</a>
        <?php endif; ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
