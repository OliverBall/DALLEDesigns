<?php
session_start();


$message = '';
$db_status = ''; 

try {

    include 'config/db.php';
} catch (Exception $e) {
    $db_status = "<div class='alert alert-danger'>Database connection failed: " . $e->getMessage() . "</div>";
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if ($conn->connect_error) {
        $message = "<div class='alert alert-danger'>Database connection failed: " . $conn->connect_error . "</div>";
    } else {

        $username = $conn->real_escape_string($_POST['username']);
        $password = $conn->real_escape_string($_POST['password']);


        $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);


        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();


            if (password_verify($password, $user['password'])) {

                $_SESSION['logged_in'] = true;
                $_SESSION['username'] = $user['username'];
                $_SESSION['user_id'] = $user['id'];

                $message = "<div class='alert alert-success'>Login successful! Redirecting...</div>";
                echo "<script>
                        setTimeout(function() {
                            window.location.href = 'account.php';
                        }, 1000); 
                      </script>";
            } else {
                $message = "<div class='alert alert-danger'>Invalid password.</div>";
            }
        } else {
            $message = "<div class='alert alert-danger'>User not found.</div>";
        }

        $stmt->close();
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" type="text/css" href="assets/css/main.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
                session_start(); 


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
                        <li class="nav-item white active">
                            <a class="nav-link white active" href="login.php">login</a>
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
                        <h1>login to your account</h1>
                        <p>view and share your custom made images</p>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 col-12 signup-left-side border-end-nav pe-5 ps-5 pt-3 pb-3">
                <?= $db_status; ?>
                    
                    <?php if (!empty($message)): ?>
                        <?= $message; ?>
                    <?php endif; ?>
                    <form action="login.php" method="POST">
                        <div class="mb-3">
                            <input type="text" class="form-control" id="username" name="username" placeholder="username" required>
                        </div>
                        <div class="mb-3">
                            <input type="password" class="form-control" id="password" name="password" placeholder="password" required>
                        </div>
                        <button type="submit" class="btn btn-secondary">login to your account <img class="arrow_forward_secondary" src="assets/images/arrow_forward_dark.svg" width="20px"></button>
                        <div class="under-login-button-secondary">
                            <div class="row">
                                <div class="col-6 text-start">
                                    <a class="under-login-button-left" href="signup.php">don't have an account? signup here</a>
                                </div>
                                <div class="col-6 text-end">
                                    <a class="under-login-button-right" href="forgot_password.php">forgot password?</a>
                                </div>
                            </div>
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
