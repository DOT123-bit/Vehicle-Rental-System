<?php
declare(strict_types=1);
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Set up your workspace | <?= e(APP_NAME) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/app.css">
</head>
<body class="setup-body">
    <main class="setup-wrap">
        <div class="brand"><span class="brand-mark">M</span><span>MILE <b>&amp; MOTOR</b></span></div>
        <section class="setup-card">
            <span class="eyebrow">ONE-TIME WORKSPACE SETUP</span>
            <h1>Let's get your<br>fleet on the road.</h1>
            <p class="muted-copy">Create your manager account. We'll add a sample fleet and a couple of bookings so you can explore the dashboard.</p>
            <?php if ($setupError !== ''): ?>
                <div class="notice notice-error" role="alert"><?= e($setupError) ?></div>
            <?php endif; ?>
            <form class="form-stack" method="post" action="setup.php">
                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                <label for="name">Your name</label>
                <input id="name" name="name" autocomplete="name" placeholder="Jordan Lee" required>
                <label for="email">Work email</label>
                <input id="email" type="email" name="email" autocomplete="email" placeholder="you@yourcompany.com" required>
                <label for="password">Create a password</label>
                <input id="password" type="password" name="password" autocomplete="new-password" minlength="8" placeholder="At least 8 characters" required>
                <button class="button button-primary button-wide" type="submit">Create workspace <span aria-hidden="true">→</span></button>
            </form>
            <a class="text-link" href="index.php">Already set up? Sign in</a>
        </section>
        <p class="setup-note">Database: <code>vehicle_rental</code> · Default XAMPP MySQL: <code>root</code></p>
    </main>
</body>
</html>