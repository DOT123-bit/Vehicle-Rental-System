<?php
declare(strict_types=1);

const APP_NAME = 'Mile & Motor';

$databaseConfig = [
    'DB_HOST' => getenv('DB_HOST') !== false ? getenv('DB_HOST') : '127.0.0.1',
    'DB_NAME' => getenv('DB_NAME') !== false ? getenv('DB_NAME') : 'vehicle_rental',
    'DB_USER' => getenv('DB_USER') !== false ? getenv('DB_USER') : 'root',
    'DB_PASS' => getenv('DB_PASS') !== false ? getenv('DB_PASS') : '',
];
$localConfigPath = __DIR__ . '/config.local.php';

if (is_file($localConfigPath)) {
    $localConfig = require $localConfigPath;
    if (is_array($localConfig)) {
        $databaseConfig = array_merge($databaseConfig, $localConfig);
    }
}

define('DB_HOST', (string) $databaseConfig['DB_HOST']);
define('DB_NAME', (string) $databaseConfig['DB_NAME']);
define('DB_USER', (string) $databaseConfig['DB_USER']);
define('DB_PASS', (string) $databaseConfig['DB_PASS']);

function db(): PDO
{
    static $connection;

    if (!$connection instanceof PDO) {
        $connection = new PDO(
            'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
            DB_USER,
            DB_PASS,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ],
        );
    }

    return $connection;
}