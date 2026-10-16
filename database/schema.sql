CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    email VARCHAR(190) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS vehicles (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    make VARCHAR(80) NOT NULL,
    model VARCHAR(100) NOT NULL,
    year SMALLINT UNSIGNED NOT NULL,
    registration VARCHAR(24) NOT NULL UNIQUE,
    category VARCHAR(40) NOT NULL,
    transmission ENUM('Automatic', 'Manual') NOT NULL DEFAULT 'Automatic',
    seats TINYINT UNSIGNED NOT NULL DEFAULT 5,
    daily_rate DECIMAL(10, 2) NOT NULL,
    image_url VARCHAR(500) NOT NULL DEFAULT '',
    is_available TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS customers (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    email VARCHAR(190) NOT NULL DEFAULT '',
    phone VARCHAR(32) NOT NULL UNIQUE,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS bookings (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    vehicle_id INT UNSIGNED NOT NULL,
    customer_id INT UNSIGNED NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    total_amount DECIMAL(10, 2) NOT NULL,
    status ENUM('Confirmed', 'Active', 'Completed', 'Cancelled') NOT NULL DEFAULT 'Confirmed',
    notes VARCHAR(500) NOT NULL DEFAULT '',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_bookings_vehicle FOREIGN KEY (vehicle_id) REFERENCES vehicles(id),
    CONSTRAINT fk_bookings_customer FOREIGN KEY (customer_id) REFERENCES customers(id),
    INDEX idx_bookings_dates (start_date, end_date),
    INDEX idx_bookings_status (status)
) ENGINE=InnoDB;