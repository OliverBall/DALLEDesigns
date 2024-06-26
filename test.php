<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Image Generator</title>
</head>
<body>
    <h1>Generate an Image with AI</h1>
    <form method="post" action="generate.php">
        <input type="text" name="prompt" placeholder="Enter a prompt for the AI">
        <button type="submit">Generate Image</button>
    </form>
    <?php if (isset($_GET['image_url'])): ?>
        <h2>Generated Image:</h2>
        <img src="<?= htmlspecialchars($_GET['image_url']) ?>" alt="Generated Image">
    <?php endif; ?>
</body>
</html>
