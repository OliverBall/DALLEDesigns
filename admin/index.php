<?php
session_start();
include '../config/db.php';


if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: ../config/login.php');
    exit();
}


$isAdmin = $_SESSION['is_admin'] ?? 0;

if ($isAdmin == 0) {
    echo "<div class='container mt-5 text-white'><h1>You're not meant to be here! Go home.</h1></div>";
    exit();
}


$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;


$sql = "SELECT id, username, email, DATE_FORMAT(created_at, '%Y-%m-%d') as signup_date FROM users LIMIT $limit OFFSET $offset";
$result = $conn->query($sql);

if (!$result) {
    die('SQL Error: ' . $conn->error);
}


$totalUsersSql = "SELECT COUNT(*) FROM users";
$totalUsersResult = $conn->query($totalUsersSql);
$totalUsers = $totalUsersResult->fetch_row()[0];
$totalPages = ceil($totalUsers / $limit);


$signupDataSql = "SELECT DATE_FORMAT(created_at, '%Y-%m-%d') as date, COUNT(*) as count FROM users GROUP BY date ORDER BY date";
$signupDataResult = $conn->query($signupDataSql);

if (!$signupDataResult) {
    die('SQL Error: ' . $conn->error);
}

$signupData = [];
while ($row = $signupDataResult->fetch_assoc()) {
    $signupData[] = $row;
}


$imageGenDataSql = "SELECT DATE_FORMAT(created_at, '%Y-%m-%d') as date, COUNT(*) as count FROM image_generations GROUP BY date ORDER BY date";
$imageGenDataResult = $conn->query($imageGenDataSql);

if (!$imageGenDataResult) {
    die('SQL Error: ' . $conn->error);
}

$imageGenData = [];
while ($row = $imageGenDataResult->fetch_assoc()) {
    $imageGenData[] = $row;
}


$recentImagesSql = "SELECT ig.image_url, ig.prompt, COALESCE(u.username, 'Unknown') as username FROM image_generations ig LEFT JOIN users u ON ig.user_id = u.id ORDER BY ig.created_at DESC LIMIT 10";
$recentImagesResult = $conn->query($recentImagesSql);

if (!$recentImagesResult) {
    die('SQL Error: ' . $conn->error);
}

$recentImages = [];
while ($row = $recentImagesResult->fetch_assoc()) {
    $recentImages[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" type="text/css" href="../assets/css/main.css">
    <style>

    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <!-- Brand/logo -->
            <a class="navbar-brand" href="#">
                <img src="../assets/images/logo.png" alt="Logo" style="height: 45px;">
            </a>
            <!-- Toggler/collapsibe Button -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <!-- Navbar links -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 ms-3 mb-lg-0"> <!-- Modified class here -->
                    <li class="nav-item">
                        <a class="nav-link" href="../index.php">home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../our-work.php">our work</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../create.php">create your own</a>
                    </li>
                </ul>
                <?php
                session_start();


                $isLoggedIn = $_SESSION['logged_in'] ?? false; 
                ?>
                <ul class="navbar-nav ml-auto border-top-nav">
                    <?php if ($isLoggedIn): ?>
                        <li class="nav-item border-end-nav white">
                            <a class="nav-link white" href="../account.php">account</a>
                        </li>
                        <li class="nav-item ms-2">
                        <a class="btn btn-danger" href="../config/logout.php" role="button">logout <span class="button-circle"><img class="arrow_forward" src="../assets/images/arrow_forward.svg" width="20px"></span></a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item border-end-nav white">
                            <a class="nav-link white" href="../contact.php">contact</a>
                        </li>
                        <li class="nav-item white">
                            <a class="nav-link white" href="../login.php">login</a>
                        </li>
                        <li class="nav-item ms-2">
                        <a class="btn btn-primary" href="../signup.php" role="button">signup <span class="button-circle"><img class="arrow_forward" src="../assets/images/arrow_forward.svg" width="20px"></span></a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5 admin">
        <h2>Admin Dashboard</h2>
        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-info"><?= $_SESSION['message']; unset($_SESSION['message']); ?></div>
        <?php endif; ?>

        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 col-12">
                <canvas id="signupChart" width="400" height="200"></canvas>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 col-12">
                <canvas id="imageGenChart" width="400" height="200"></canvas>
            </div>
        </div>

        <table class="table table-dark table-striped mt-5">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Sign-Up Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['id']); ?></td>
                    <td><?= htmlspecialchars($row['username']); ?></td>
                    <td><?= htmlspecialchars($row['email']); ?></td>
                    <td><?= htmlspecialchars($row['signup_date']); ?></td>
                    <td>
                        <button class="btn btn-outline-warning btn-sm" data-bs-toggle="modal" data-bs-target="#updateUserModal" data-id="<?= $row['id']; ?>" data-username="<?= htmlspecialchars($row['username']); ?>" data-email="<?= htmlspecialchars($row['email']); ?>">Update</button>
                        <button class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteUserModal" data-id="<?= $row['id']; ?>">Delete</button>
                        <?php
                        $userId = $row['id'];
                        $generatedImagesSql = "SELECT COUNT(*) as count FROM image_generations WHERE user_id = $userId";
                        $generatedImagesResult = $conn->query($generatedImagesSql);
                        $generatedImagesCount = $generatedImagesResult->fetch_assoc()['count'];
                        if ($generatedImagesCount > 0): ?>
                            <a href="view_generated_images.php?user_id=<?= $row['id']; ?>" class="btn btn-outline-info btn-sm">View Generated Images</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <nav aria-label="Page navigation example">
            <ul class="pagination">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?= $i == $page ? 'active' : ''; ?>">
                        <a class="page-link" href="index.php?page=<?= $i; ?>"><?= $i; ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>

        <h3>Recently Generated Images</h3>
        <div class="row" style="color:#fff!important;">
            <?php foreach ($recentImages as $image): ?>
                <div class="col-md-4">
                    <div class="card mb-4" style="color:#fff!important;">
                        <img src="<?= htmlspecialchars($image['image_url']); ?>" class="card-img-top" alt="Generated Image">
                        <div class="card-body" style="color:#fff!important;">
                            <p class="card-text"><strong>Prompt:</strong> <?= htmlspecialchars($image['prompt']); ?></p>
                            <p class="card-text"><strong>User:</strong> <?= htmlspecialchars($image['username']); ?></p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="modal fade" id="updateUserModal" tabindex="-1" aria-labelledby="updateUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content bg-dark text-white">
                <form id="updateUserForm" action="update_user.php" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title" id="updateUserModalLabel">Update User</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id" id="userId">
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control bg-dark text-white" id="username" name="username" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control bg-dark text-white" id="email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password (leave blank to keep current)</label>
                            <input type="password" class="form-control bg-dark text-white" id="password" name="password">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="deleteUserModal" tabindex="-1" aria-labelledby="deleteUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content bg-dark text-white">
                <form id="deleteUserForm" action="delete_user.php" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteUserModalLabel">Delete User</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id" id="deleteUserId">
                        <p>Are you sure you want to delete this user?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>

        document.getElementById('updateUserModal').addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            var id = button.getAttribute('data-id');
            var username = button.getAttribute('data-username');
            var email = button.getAttribute('data-email');

            var modal = this;
            modal.querySelector('#userId').value = id;
            modal.querySelector('#username').value = username;
            modal.querySelector('#email').value = email;
        });


        document.getElementById('deleteUserModal').addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            var id = button.getAttribute('data-id');

            var modal = this;
            modal.querySelector('#deleteUserId').value = id;
        });


        const signupData = <?= json_encode($signupData); ?>;
        const signupLabels = signupData.map(data => data.date);
        const signupCounts = signupData.map(data => data.count);


        const signupCtx = document.getElementById('signupChart').getContext('2d');
        const signupChart = new Chart(signupCtx, {
            type: 'line',
            data: {
                labels: signupLabels,
                datasets: [{
                    label: 'Sign-Ups Per Day',
                    data: signupCounts,
                    borderColor: 'rgba(75, 192, 192, 1)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                },
                plugins: {
                    legend: {
                        labels: {
                            color: '#ffffff'
                        }
                    }
                }
            }
        });


        const imageGenData = <?= json_encode($imageGenData); ?>;
        const imageGenLabels = imageGenData.map(data => data.date);
        const imageGenCounts = imageGenData.map(data => data.count);


        const imageGenCtx = document.getElementById('imageGenChart').getContext('2d');
        const imageGenChart = new Chart(imageGenCtx, {
            type: 'line',
            data: {
                labels: imageGenLabels,
                datasets: [{
                    label: 'Images Generated Per Day',
                    data: imageGenCounts,
                    borderColor: 'rgba(255, 99, 132, 1)',
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                },
                plugins: {
                    legend: {
                        labels: {
                            color: '#ffffff'
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>

<?php
$conn->close();
?>
