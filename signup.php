<?php
session_start(); 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include 'config/db.php';

    $username = $conn->real_escape_string($_POST['username']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = $conn->real_escape_string($_POST['password']);
    $hashed_password = password_hash($password, PASSWORD_DEFAULT); 

    $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param("sss", $username, $email, $hashed_password);

        if ($stmt->execute()) {
            $_SESSION['logged_in'] = true;
            $_SESSION['user_id'] = $stmt->insert_id; 
            $_SESSION['username'] = $username; 
            $_SESSION['email'] = $email;
            $_SESSION['message'] = "<div class='alert alert-success'>Registration successful! Redirecting...</div>";
            
            echo "<script>
                    setTimeout(function() {
                        window.location.href = 'account.php';
                    }, 3000);
                  </script>";
        } else {
            $_SESSION['message'] = "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>";
        }
        $stmt->close();
    } else {
        $_SESSION['message'] = "<div class='alert alert-danger'>Error preparing statement: " . $conn->error . "</div>";
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="assets/css/main.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <img src="assets/images/logo.png" alt="Logo" style="height: 45px;">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 ms-3 mb-lg-0"> 
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="our-work.php">our work</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="create.php">create your own</a>
                    </li>
                </ul>
                <?php
                $isLoggedIn = $_SESSION['logged_in'] ?? false; 
                ?>
                <ul class="navbar-nav ml-auto border-top-nav">
                    <?php if ($isLoggedIn): ?>
                        <li class="nav-item border-end-nav white">
                            <a class="nav-link white" href="account.php">account</a>
                        </li>
                        <li class="nav-item ms-2">
                        <a class="btn btn-danger" href="config/logout.php" role="button">logout <span class="button-circle"><img class="arrow_forward" src="assets/images/arrow_forward.svg" width="20px"></span></a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item border-end-nav white">
                            <a class="nav-link white" href="contact.php">contact</a>
                        </li>
                        <li class="nav-item white">
                            <a class="nav-link white" href="login.php">login</a>
                        </li>
                        <li class="nav-item ms-2">
                        <a class="btn btn-primary" href="signup.php" role="button">signup <span class="button-circle"><img class="arrow_forward" src="assets/images/arrow_forward.svg" width="20px"></span></a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
    <div class="bg">
        <div class="container pt-5 pb-5">
            <div class="row">
                <div class="col-12 text-center bg-header pb-4">
                        <h1>sign up for an account</h1>
                        <p>view and share your custom made images</p>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 col-12 signup-left-side border-end-nav pe-5 ps-5 pt-3 pb-3">
                <?php if (isset($_SESSION['message'])): ?>
                        <?= $_SESSION['message']; ?>
                        <?php unset($_SESSION['message']); ?>
                    <?php endif; ?>
                    <form action="signup.php" method="POST">
                        <div class="mb-3">
                            <input type="text" class="form-control" id="username" name="username" placeholder="username"  required>
                        </div>
                        <div class="mb-3">
                            <input type="email" class="form-control" id="email" name="email" placeholder="email" required>
                        </div>
                        <div class="mb-3">
                            <input type="password" class="form-control" id="password" name="password" placeholder="password" required>
                        </div>
                        <button type="submit" class="btn btn-secondary">sign up for your account <img class="arrow_forward_secondary" src="assets/images/arrow_forward_dark.svg" width="20px"></button>
                        <div class="under-login-button">
                            <a class="under-login-button" href="login.php">already have an account? login here</a>
                        </div>
                    </form>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 col-12  signup-right-side">

                </div>
            </div>
            
        </div>
    </div>

    <div class="second_footer">
        <div class="container pt-3 pb-3">
            <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 col-12  copyright-links-mobile pb-3">
                   <ul>
                        <li><a href="index.php">cookies</a></li>
                        <li><a href="index.php">privacy policy</a></li>
                        <li><a href="index.php">terms of service</a></li>
                    </ul>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 col-12  copyright">
                    <p>© 2024 Copyright Dall-E Designs - All Rights Reserved</p>
                    <p>copy & website by <a href="index.php">oliver ball</a>.</p>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 col-12  copyright-links">
                   <ul>
                        <li><a href="index.php">cookies</a></li>
                        <li><a href="index.php">privacy policy</a></li>
                        <li><a href="index.php">terms of service</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
