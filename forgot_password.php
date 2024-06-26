<?php
session_start();
include 'config/db.php';

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $conn->real_escape_string($_POST['email']);


    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {

        $token = bin2hex(random_bytes(32));
        $stmt->close();


        $expiry = time() + 3600; 
        $stmt = $conn->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?)");
        $stmt->bind_param("ssi", $email, $token, $expiry);
        $stmt->execute();
        $stmt->close();


        echo "<script src='https://cdn.emailjs.com/dist/email.min.js'></script>";
        echo "<script>
            (function() {
                emailjs.init('publickey');
                emailjs.send('serviceid', 'templateid', {
                    to_email: '$email',  
                    reset_link: 'http://olivers-mbp/dalle/change_password.php?token=$token'
                }).then(function(response) {
                    document.getElementById('message').innerHTML = '<div class=\"alert alert-success\">Please check your email for instructions</div>';
                }, function(error) {
                    document.getElementById('message').innerHTML = '<div class=\"alert alert-danger\">Error: ' + JSON.stringify(error) + '</div>';
                });
            })();
        </script>";
    } else {

        $message = '<div class="alert alert-danger">Email not found</div>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script type="text/javascript" src="https://cdn.emailjs.com/dist/email.min.js"></script>
</head>
<body>
    <div class="container mt-5">
        <h2>Forgot Password</h2>
        <div id="message">
            <?php
            if (!empty($message)) {
                echo $message;
            }
            ?>
        </div>
        <form action="forgot_password.php" method="POST">
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <button type="submit" class="btn btn-primary">Change Password</button>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
