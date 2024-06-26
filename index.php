<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Navbar Example</title>
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
                    <li class="nav-item active">
                        <a class="nav-link active" href="index.php">home</a>
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

    <div class="hero">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 col-md-6 col-xs-12 col-sm-12 col-12">
                        <h4>ai generated garment designs<h4>
                </div>
                <div class="col-lg-4 col-md-6 col-xs-12 col-sm-12 col-12">
                        hi
                </div>
            </div>
        </div>
    </div>

    <h1>Generate an Image with AI</h1>
    <form method="post" action="config/generate.php">
        <input type="text" name="prompt" placeholder="Enter a prompt for the AI">
        <button type="submit">Generate Image</button>
    </form>
    <?php if (isset($_GET['image_url'])): ?>
        <h2>Generated Image:</h2>
        <img src="<?= htmlspecialchars($_GET['image_url']) ?>" alt="Generated Image">
    <?php endif; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
