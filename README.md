# Mile & Motor

A small vehicle-rental management system built with PHP, MySQL, HTML, CSS, and JavaScript for XAMPP.

## Run it with XAMPP

1. Put this project folder in `C:\xampp\htdocs\Vehicle Rental System`.
2. Open the XAMPP Control Panel and start **Apache** and **MySQL**.
3. Open `http://localhost/phpmyadmin`, create a database named `vehicle_rental`, select it, and use **Import** to run `database/schema.sql`.
4. Visit `http://localhost/Vehicle%20Rental%20System/setup.php` and create your manager account. The first-time setup adds a sample fleet, customers, and upcoming bookings.
5. Sign in at `http://localhost/Vehicle%20Rental%20System/`.

XAMPP defaults are MySQL on `127.0.0.1`, database `vehicle_rental`, user `root`, and a blank password. These defaults are in `config.php`.

## Host it with PHP and MySQL

This project works on PHP/MySQL shared hosting with cPanel (or an equivalent control panel). A domain is optional if your provider supplies a temporary URL. The provider must support PHP with PDO MySQL and MySQL/MariaDB.

1. In your hosting control panel, open **MySQL Databases** (or **Database Wizard**). Create a database and database user, then grant that user all privileges on the database. Copy the full database name, username, password, and host; cPanel commonly prefixes database names and usernames with your account name.
2. In the hosting File Manager, upload the project files into `public_html` (or a subfolder if you want the app under a URL path). Include `.htaccess`. Do not upload `config.local.example.php` as your live config; it is only a template. You can keep `database/schema.sql` on your computer and upload it temporarily only if needed for import.
3. In the hosting control panel's phpMyAdmin, select the database you created and import `database/schema.sql`. The schema does not create or select a database, so it works with database names assigned by shared hosts.
4. Copy `config.local.example.php` to `config.local.php` in the project root. Edit `config.local.php` with the exact database values from your host. Replace every example value, especially the database password. The live configuration file is excluded from Git and blocked from direct web access by `.htaccess`.
5. Visit `https://your-domain/setup.php` (or the provider's temporary URL plus `/setup.php`) and create your manager account. The first-time setup adds sample fleet records.
6. Sign in and confirm the dashboard loads. Then remove `setup.php` from the live site using your hosting File Manager. Turn on your host's free SSL certificate before sharing the site.

Never use XAMPP's `root` / blank-password database credentials on a public website. Use the database user and password created by your hosting provider. If your host does not support Apache `.htaccess`, remove the SQL file from the public web folder after importing it and use that host's access-control settings to protect configuration files.

## What is included

- Password-hashed manager account, session login, and CSRF-protected forms.
- Overview dashboard with fleet availability, upcoming bookings, customers, and revenue.
- Vehicle fleet with registration, category, rate, transmission, seats, and availability controls.
- Booking creation with date validation, customer records, rate calculation, and prevention of overlapping reservations.
- Booking status updates and customer booking history.
- Responsive layout with searchable fleet, booking, and customer tables.

Sample vehicle photos load from Unsplash and require an internet connection. The rest of the app runs locally in XAMPP.