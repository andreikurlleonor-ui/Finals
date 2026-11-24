<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.html");
    exit;
}

$name = htmlspecialchars($_SESSION["user_name"] ?? "there", ENT_QUOTES, "UTF-8");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | GreeNgage</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="navbar">
        <a href="index.html" class="logo">🌱 GreeNgage</a>
        <a href="index.html" class="login-btn">Back to Home</a>
    </header>

    <main class="dashboard-main">
        <section class="dashboard-welcome">
            <span class="hero-tag">🌿 YOUR GREEN JOURNEY</span>
            <h1>Welcome back, <span><?php echo $name; ?>.</span></h1>
            <p>
                Your GreeNgage account is ready. Explore campus sustainability
                quests and start turning small actions into a greener impact.
            </p>
            <div class="dashboard-actions">
                <a href="index.html#quests">Explore Quests</a>
                <a href="index.html">Visit Home</a>
            </div>
        </section>
    </main>
</body>
</html>
