<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['prompt'])) {

    if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
        header('Location: login.php');
        exit();
    }


    $apiKey = 'openaikeyhere';


    $ch = curl_init();


    curl_setopt($ch, CURLOPT_URL, 'https://api.openai.com/v1/images/generations');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
        'prompt' => $_POST['prompt'],
        'n' => 1,
        'size' => '1024x1024'
    ]));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $apiKey
    ]);


    $response = curl_exec($ch);
    curl_close($ch);


    $result = json_decode($response, true);


    $image_url = $result['data'][0]['url'] ?? '';


    $userId = $_SESSION['user_id'];
    $stmt = $conn->prepare("INSERT INTO image_generations (user_id, prompt, image_url) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $userId, $_POST['prompt'], $image_url);
    $stmt->execute();
    $stmt->close();
    $conn->close();


    header('Location: ../index.php?image_url=' . urlencode($image_url));
    exit;
}


header('Location: ../index.php');
exit;
?>
