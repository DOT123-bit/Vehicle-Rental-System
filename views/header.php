<?php
declare(strict_types=1);

$userName = (string) ($_SESSION['user']['name'] ?? 'Manager');
$initials = implode('', array_map(static fn (string $part): string => strtoupper(substr($part, 0, 1)), array_slice(explode(' ', $userName), 0, 2)));
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#f4f5f1">
    <title><?= e($pageTitle) ?> | <?= e(APP_NAME) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/app.css">
</head>
<body class="app-body">
    <div class="app-shell">
        <aside class="sidebar" id="sidebar">
            <a class="brand brand-light" href="index.php"><span class="brand-mark">M</span><span>MILE <b>&amp; MOTOR</b></span></a>
            <div class="workspace-label">WORKSPACE <span class="workspace-pip"></span></div>
            <nav class="main-nav" aria-label="Main navigation">
                <a class="nav-link <?= $page === 'dashboard' ? 'is-active' : '' ?>" href="index.php"><span class="nav-index">01</span>Overview</a>
                <a class="nav-link <?= $page === 'vehicles' ? 'is-active' : '' ?>" href="index.php?page=vehicles"><span class="nav-index">02</span>Fleet</a>
                <a class="nav-link <?= $page === 'bookings' ? 'is-active' : '' ?>" href="index.php?page=bookings"><span class="nav-index">03</span>Bookings</a>
                <a class="nav-link <?= $page === 'customers' ? 'is-active' : '' ?>" href="index.php?page=customers"><span class="nav-index">04</span>Customers</a>
            </nav>
            <div class="sidebar-bottom">
                <div class="sidebar-caption">KEEP IT MOVING</div>
                <p>Your next great<br>journey starts here.</p>
                <span class="sidebar-rule"></span>
                <span class="sidebar-version">MILE &amp; MOTOR <span>·</span> 1.0</span>
            </div>
        </aside>
        <div class="app-main">
            <header class="topbar">
                <button class="icon-button menu-toggle" type="button" aria-label="Open navigation" aria-expanded="false" data-menu-toggle><span></span><span></span></button>
                <div class="breadcrumb"><span>WORKSPACE</span><span class="breadcrumb-slash">/</span><strong><?= e(strtoupper($pageTitle)) ?></strong></div>
                <div class="topbar-actions">
                    <span class="today-label"><?= date('D, M j') ?></span>
                    <span class="topbar-divider"></span>
                    <div class="account-chip"><span class="account-avatar"><?= e($initials) ?></span><span class="account-name"><?= e($userName) ?></span></div>
                    <form action="actions.php" method="post" class="logout-form">
                        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                        <input type="hidden" name="intent" value="logout">
                        <button class="text-button" type="submit">Sign out</button>
                    </form>
                </div>
            </header>
            <main class="page-content">
                <?php if ($flashMessage): ?>
                    <div class="notice notice-<?= e($flashMessage['type']) ?>" role="status" data-notice><?= e($flashMessage['message']) ?><button class="notice-dismiss" type="button" aria-label="Dismiss notification">×</button></div>
                <?php endif; ?>