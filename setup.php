<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

$setupError = '';
$connection = null;

try {
    $connection = db();
    $hasAdmin = (int) $connection->query('SELECT COUNT(*) FROM users')->fetchColumn() > 0;
} catch (PDOException) {
    $setupError = 'Create the database and tables using database/schema.sql before continuing.';
    $hasAdmin = false;
}

if ($hasAdmin && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('index.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $name = trim((string) ($_POST['name'] ?? ''));
    $email = filter_var(trim((string) ($_POST['email'] ?? '')), FILTER_VALIDATE_EMAIL);
    $password = (string) ($_POST['password'] ?? '');

    if ($setupError !== '') {
        $setupError = 'Import database/schema.sql in phpMyAdmin, then reload this page.';
    } elseif ($hasAdmin) {
        $setupError = 'This system is already set up. Sign in from the dashboard.';
    } elseif ($name === '' || $email === false || strlen($password) < 8) {
        $setupError = 'Enter your name, a valid email, and a password with at least 8 characters.';
    } else {
        try {
            $connection->beginTransaction();
            $admin = $connection->prepare('INSERT INTO users (name, email, password_hash) VALUES (?, ?, ?)');
            $admin->execute([$name, $email, password_hash($password, PASSWORD_DEFAULT)]);

            $fleet = [
                ['Toyota', 'Corolla', 2024, 'VR-2048', 'Sedan', 'Automatic', 5, 54, 'photo-1552519507-da3b142c6e3d'],
                ['Honda', 'CR-V', 2023, 'VR-3192', 'SUV', 'Automatic', 5, 82, 'photo-1519641471654-76ce0107ad1b'],
                ['BMW', '3 Series', 2024, 'VR-4421', 'Luxury', 'Automatic', 5, 115, 'photo-1555215695-3004980ad54e'],
                ['Volkswagen', 'Golf', 2022, 'VR-1076', 'Economy', 'Manual', 5, 46, 'photo-1549317661-bd32c8ce0db2'],
            ];
            $vehicleInsert = $connection->prepare(
                'INSERT INTO vehicles (make, model, year, registration, category, transmission, seats, daily_rate, image_url) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)'
            );
            foreach ($fleet as [$make, $model, $year, $registration, $category, $transmission, $seats, $rate, $image]) {
                $vehicleInsert->execute([$make, $model, $year, $registration, $category, $transmission, $seats, $rate, 'https://images.unsplash.com/' . $image . '?auto=format&fit=crop&w=640&q=80']);
            }

            $customers = [
                ['Olivia Chen', 'olivia@example.com', '+1 415 555 0134'],
                ['Noah Williams', 'noah@example.com', '+1 415 555 0187'],
                ['Amara Okafor', 'amara@example.com', '+1 415 555 0112'],
            ];
            $customerInsert = $connection->prepare('INSERT INTO customers (name, email, phone) VALUES (?, ?, ?)');
            foreach ($customers as $customer) {
                $customerInsert->execute($customer);
            }

            $bookingInsert = $connection->prepare(
                'INSERT INTO bookings (vehicle_id, customer_id, start_date, end_date, total_amount, status) VALUES (?, ?, ?, ?, ?, ?)'
            );
            $firstStart = new DateTimeImmutable('+2 days');
            $firstEnd = $firstStart->modify('+2 days');
            $secondStart = new DateTimeImmutable('+4 days');
            $secondEnd = $secondStart->modify('+1 day');
            $bookingInsert->execute([1, 1, $firstStart->format('Y-m-d'), $firstEnd->format('Y-m-d'), 162, 'Confirmed']);
            $bookingInsert->execute([2, 2, $secondStart->format('Y-m-d'), $secondEnd->format('Y-m-d'), 164, 'Confirmed']);
            $connection->commit();
            flash('success', 'Your workspace is ready. Sign in with your new account.');
            redirect('index.php');
        } catch (Throwable) {
            if ($connection->inTransaction()) {
                $connection->rollBack();
            }
            $setupError = 'Setup could not be completed. Check that the schema is imported and try again.';
        }
    }
}

require __DIR__ . '/views/setup.php';