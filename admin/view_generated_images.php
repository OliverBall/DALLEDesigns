<?php
session_start();
include '../config/db.php';

$userId = isset($_GET['user_id']) ? (int)$_GET['user_id'] : 0;

if ($userId > 0) {
    $sql = "SELECT ig.image_url, ig.prompt, u.username FROM image_generations ig JOIN users u ON ig.user_id = u.id WHERE ig.user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    $images = [];
    while ($row = $result->fetch_assoc()) {
        $images[] = $row;
    }

    $stmt->close();
    $conn->close();
} else {
    $_SESSION['message'] = "Invalid user ID.";
    header("Location: ../admin.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generated Images</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #121212;
            color: #ffffff;
        }
        .card {
            background-color: #1e1e1e;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h2>Generated Images for User ID: <?= htmlspecialchars($userId); ?></h2>
        <div class="row">
            <?php foreach ($images as $image): ?>
                <div class="col-md-4">
                    <div class="card mb-4">
                        <img src="<?= htmlspecialchars($image['image_url']); ?>" class="card-img-top" alt="Generated Image">
                        <div class="card-body" style="color:#fff!important;">
                            <p class="card-text"><strong>Prompt:</strong> <?= htmlspecialchars($image['prompt']); ?></p>
                            <p class="card-text"><strong>User:</strong> <?= htmlspecialchars($image['username']); ?></p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <a href="index.php" class="btn btn-primary">Back to Admin Dashboard</a>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
