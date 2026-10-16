<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('index.php');
}

verify_csrf();
$intent = (string) ($_POST['intent'] ?? '');

if ($intent === 'login') {
    $email = filter_var(trim((string) ($_POST['email'] ?? '')), FILTER_VALIDATE_EMAIL);
    $password = (string) ($_POST['password'] ?? '');

    if ($email !== false) {
        $statement = db()->prepare('SELECT id, name, email, password_hash FROM users WHERE email = ?');
        $statement->execute([$email]);
        $user = $statement->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            session_regenerate_id(true);
            $_SESSION['user'] = ['id' => (int) $user['id'], 'name' => $user['name'], 'email' => $user['email']];
            flash('success', 'Welcome back, ' . $user['name'] . '.');
            redirect('index.php');
        }
    }

    flash('error', 'That email and password combination did not match.');
    redirect('index.php');
}

if ($intent === 'logout') {
    $_SESSION = [];
    session_destroy();
    redirect('index.php');
}

if (!is_signed_in()) {
    redirect('index.php');
}

$returnPage = in_array($_POST['return_page'] ?? '', ['dashboard', 'vehicles', 'bookings', 'customers'], true)
    ? (string) $_POST['return_page']
    : 'dashboard';

try {
    if ($intent === 'add_vehicle') {
        $make = trim((string) ($_POST['make'] ?? ''));
        $model = trim((string) ($_POST['model'] ?? ''));
        $year = filter_var($_POST['year'] ?? null, FILTER_VALIDATE_INT);
        $registration = strtoupper(trim((string) ($_POST['registration'] ?? '')));
        $category = (string) ($_POST['category'] ?? '');
        $transmission = (string) ($_POST['transmission'] ?? 'Automatic');
        $seats = filter_var($_POST['seats'] ?? null, FILTER_VALIDATE_INT);
        $rate = filter_var($_POST['daily_rate'] ?? null, FILTER_VALIDATE_FLOAT);
        $allowedCategories = ['Economy', 'Sedan', 'SUV', 'Luxury', 'Van'];

        if ($make === '' || $model === '' || !$year || $year < 1980 || $year > (int) date('Y') + 1
            || $registration === '' || !in_array($category, $allowedCategories, true)
            || !in_array($transmission, ['Automatic', 'Manual'], true)
            || !$seats || $seats < 1 || $seats > 12 || $rate === false || $rate <= 0) {
            throw new InvalidArgumentException('Check the vehicle details and try again.');
        }

        $statement = db()->prepare(
            'INSERT INTO vehicles (make, model, year, registration, category, transmission, seats, daily_rate) VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $statement->execute([$make, $model, $year, $registration, $category, $transmission, $seats, $rate]);
        flash('success', $make . ' ' . $model . ' was added to your fleet.');
    } elseif ($intent === 'toggle_vehicle') {
        $vehicleId = filter_var($_POST['vehicle_id'] ?? null, FILTER_VALIDATE_INT);
        if (!$vehicleId) {
            throw new InvalidArgumentException('Choose a valid vehicle.');
        }

        $statement = db()->prepare('UPDATE vehicles SET is_available = 1 - is_available WHERE id = ?');
        $statement->execute([$vehicleId]);
        flash('success', 'Vehicle availability updated.');
    } elseif ($intent === 'add_booking') {
        $vehicleId = filter_var($_POST['vehicle_id'] ?? null, FILTER_VALIDATE_INT);
        $name = trim((string) ($_POST['customer_name'] ?? ''));
        $phone = trim((string) ($_POST['customer_phone'] ?? ''));
        $email = trim((string) ($_POST['customer_email'] ?? ''));
        $startDate = (string) ($_POST['start_date'] ?? '');
        $endDate = (string) ($_POST['end_date'] ?? '');
        $notes = trim((string) ($_POST['notes'] ?? ''));

        $start = DateTimeImmutable::createFromFormat('!Y-m-d', $startDate);
        $end = DateTimeImmutable::createFromFormat('!Y-m-d', $endDate);
        $today = new DateTimeImmutable('today');
        if (!$vehicleId || $name === '' || $phone === '' || !$start || !$end || $start < $today || $end < $start
            || $start->format('Y-m-d') !== $startDate || $end->format('Y-m-d') !== $endDate
            || (strlen($email) > 0 && !filter_var($email, FILTER_VALIDATE_EMAIL))) {
            throw new InvalidArgumentException('Check the booking details and choose valid dates.');
        }

        $connection = db();
        $connection->beginTransaction();
        $vehicleQuery = $connection->prepare('SELECT id, daily_rate, is_available FROM vehicles WHERE id = ? FOR UPDATE');
        $vehicleQuery->execute([$vehicleId]);
        $vehicle = $vehicleQuery->fetch();

        if (!$vehicle || !$vehicle['is_available']) {
            throw new InvalidArgumentException('That vehicle is not available for booking.');
        }

        $overlapQuery = $connection->prepare(
            "SELECT COUNT(*) FROM bookings WHERE vehicle_id = ? AND status IN ('Confirmed', 'Active') AND start_date <= ? AND end_date >= ?"
        );
        $overlapQuery->execute([$vehicleId, $endDate, $startDate]);
        if ((int) $overlapQuery->fetchColumn() > 0) {
            throw new InvalidArgumentException('That vehicle already has a booking during those dates.');
        }

        $customerQuery = $connection->prepare('SELECT id FROM customers WHERE phone = ?');
        $customerQuery->execute([$phone]);
        $customerId = $customerQuery->fetchColumn();
        if ($customerId === false) {
            $customerInsert = $connection->prepare('INSERT INTO customers (name, email, phone) VALUES (?, ?, ?)');
            $customerInsert->execute([$name, $email, $phone]);
            $customerId = (int) $connection->lastInsertId();
        } else {
            $customerId = (int) $customerId;
            $customerUpdate = $connection->prepare('UPDATE customers SET name = ?, email = ? WHERE id = ?');
            $customerUpdate->execute([$name, $email, $customerId]);
        }

        $days = (int) $start->diff($end)->days + 1;
        $bookingInsert = $connection->prepare(
            'INSERT INTO bookings (vehicle_id, customer_id, start_date, end_date, total_amount, notes) VALUES (?, ?, ?, ?, ?, ?)'
        );
        $bookingInsert->execute([$vehicleId, $customerId, $startDate, $endDate, $days * (float) $vehicle['daily_rate'], $notes]);
        $connection->commit();
        flash('success', 'Booking confirmed and customer details saved.');
    } elseif ($intent === 'update_booking_status') {
        $bookingId = filter_var($_POST['booking_id'] ?? null, FILTER_VALIDATE_INT);
        $status = (string) ($_POST['status'] ?? '');
        if (!$bookingId || !in_array($status, ['Confirmed', 'Active', 'Completed', 'Cancelled'], true)) {
            throw new InvalidArgumentException('Choose a valid booking status.');
        }

        $statement = db()->prepare('UPDATE bookings SET status = ? WHERE id = ?');
        $statement->execute([$status, $bookingId]);
        flash('success', 'Booking status updated.');
    } else {
        throw new InvalidArgumentException('That action is not available.');
    }
} catch (InvalidArgumentException $exception) {
    if (isset($connection) && $connection instanceof PDO && $connection->inTransaction()) {
        $connection->rollBack();
    }
    flash('error', $exception->getMessage());
} catch (PDOException $exception) {
    if (isset($connection) && $connection instanceof PDO && $connection->inTransaction()) {
        $connection->rollBack();
    }
    $message = $intent === 'add_vehicle'
        ? 'That registration number is already in your fleet.'
        : 'This change could not be saved. Please check the details and try again.';
    flash('error', $message);
}

redirect('index.php?page=' . $returnPage);