<?php
declare(strict_types=1);

$message = take_flash();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#152a24">
    <title>Sign in | <?= e(APP_NAME) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/app.css">
</head>
<body class="login-body">
    <main class="login-layout">
        <section class="login-story">
            <div class="brand brand-light"><span class="brand-mark">M</span><span>MILE <b>&amp; MOTOR</b></span></div>
            <div class="login-story-copy">
                <span class="eyebrow eyebrow-light"><span class="live-dot"></span> YOUR FLEET, IN MOTION</span>
                <h1>Every journey<br>starts <em>somewhere.</em></h1>
                <p>A calmer way to keep your vehicles, bookings, and people moving together.</p>
            </div>
            <div class="story-footer"><span>01 / OPERATIONS</span><span>EST. FOR THE OPEN ROAD</span></div>
            <div class="story-image" role="img" aria-label="A car ready for the open road"></div>
        </section>
        <section class="login-panel">
            <div class="login-panel-top"><span class="eyebrow">OPERATIONS PORTAL</span><a href="setup.php">First visit? Set up your account <span aria-hidden="true">↗</span></a></div>
            <div class="login-form-wrap">
                <span class="eyebrow">GOOD TO SEE YOU</span>
                <h2>Welcome back.</h2>
                <p class="muted-copy">Sign in to pick up where your fleet left off.</p>
                <?php if ($message): ?>
                    <div class="notice notice-<?= e($message['type']) ?>" role="status"><?= e($message['message']) ?></div>
                <?php endif; ?>
                <form class="form-stack" action="actions.php" method="post">
                    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                    <input type="hidden" name="intent" value="login">
                    <label for="email">Work email</label>
                    <input id="email" type="email" name="email" autocomplete="username" placeholder="you@yourcompany.com" required autofocus>
                    <div class="label-row"><label for="password">Password</label></div>
                    <input id="password" type="password" name="password" autocomplete="current-password" placeholder="Enter your password" required>
                    <button class="button button-primary button-wide" type="submit">Sign in <span aria-hidden="true">→</span></button>
                </form>
                <p class="form-footnote">Your workspace is private to your team.</p>
            </div>
            <div class="login-panel-bottom"><span>© <?= date('Y') ?> MILE &amp; MOTOR</span><span>MADE FOR THE MILES AHEAD</span></div>
        </section>
    </main>
    <script src="assets/app.js" defer></script>
</body>
</html>