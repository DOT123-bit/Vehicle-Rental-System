<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

if (!is_signed_in()) {
    require __DIR__ . '/views/login.php';
    exit;
}

$page = (string) ($_GET['page'] ?? 'dashboard');
if (!in_array($page, ['dashboard', 'vehicles', 'bookings', 'customers'], true)) {
    $page = 'dashboard';
}

$pageTitles = [
    'dashboard' => 'Overview',
    'vehicles' => 'Fleet',
    'bookings' => 'Bookings',
    'customers' => 'Customers',
];
$pageTitle = $pageTitles[$page];
$flashMessage = take_flash();
$connectionError = '';

try {
    $connection = db();

    if ($page === 'dashboard') {
        $stats = [
            'fleet' => (int) $connection->query('SELECT COUNT(*) FROM vehicles')->fetchColumn(),
            'available' => (int) $connection->query('SELECT COUNT(*) FROM vehicles WHERE is_available = 1')->fetchColumn(),
            'bookings' => (int) $connection->query("SELECT COUNT(*) FROM bookings WHERE status IN ('Confirmed', 'Active')")->fetchColumn(),
            'customers' => (int) $connection->query('SELECT COUNT(*) FROM customers')->fetchColumn(),
            'revenue' => (float) $connection->query("SELECT COALESCE(SUM(total_amount), 0) FROM bookings WHERE status IN ('Active', 'Completed')")->fetchColumn(),
        ];
        $upcoming = $connection->query(
            "SELECT b.id, b.start_date, b.end_date, b.status, b.total_amount, c.name AS customer_name, v.make, v.model, v.image_url FROM bookings b JOIN customers c ON c.id = b.customer_id JOIN vehicles v ON v.id = b.vehicle_id WHERE b.status IN ('Confirmed', 'Active') ORDER BY b.start_date LIMIT 5"
        )->fetchAll();
        $fleetPreview = $connection->query('SELECT id, make, model, year, category, daily_rate, registration, is_available, image_url FROM vehicles ORDER BY created_at DESC LIMIT 4')->fetchAll();
        $categoryCounts = $connection->query('SELECT category, COUNT(*) AS count FROM vehicles GROUP BY category ORDER BY count DESC LIMIT 4')->fetchAll();
    } elseif ($page === 'vehicles') {
        $vehicles = $connection->query('SELECT * FROM vehicles ORDER BY created_at DESC')->fetchAll();
    } elseif ($page === 'bookings') {
        $bookings = $connection->query(
            'SELECT b.*, c.name AS customer_name, c.phone AS customer_phone, v.make, v.model, v.registration FROM bookings b JOIN customers c ON c.id = b.customer_id JOIN vehicles v ON v.id = b.vehicle_id ORDER BY b.start_date DESC, b.id DESC'
        )->fetchAll();
        $bookableVehicles = $connection->query('SELECT id, make, model, registration, daily_rate FROM vehicles WHERE is_available = 1 ORDER BY make, model')->fetchAll();
    } elseif ($page === 'customers') {
        $customers = $connection->query(
            'SELECT c.*, COUNT(b.id) AS booking_count, COALESCE(SUM(CASE WHEN b.status != "Cancelled" THEN b.total_amount ELSE 0 END), 0) AS lifetime_value FROM customers c LEFT JOIN bookings b ON b.customer_id = c.id GROUP BY c.id ORDER BY c.created_at DESC'
        )->fetchAll();
    }
} catch (PDOException) {
    $connectionError = 'The database is not connected. Import database/schema.sql in phpMyAdmin and check the database settings in config.php.';
}

require __DIR__ . '/views/header.php';

if ($connectionError !== '') {
    echo '<section class="empty-state"><span class="eyebrow">Database connection</span><h2>We could not reach your database.</h2><p>' . e($connectionError) . '</p><a class="button button-primary" href="setup.php">Open setup</a></section>';
} else {
    require __DIR__ . '/views/' . $page . '.php';
}

require __DIR__ . '/views/footer.php';