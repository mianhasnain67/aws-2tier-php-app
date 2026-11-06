<?php
declare(strict_types=1);

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$host = getenv('DB_HOST');
$username = getenv('DB_USER');
$password = getenv('DB_PASSWORD');
$database = getenv('DB_NAME');
$port = (int) (getenv('DB_PORT') ?: 3306);

if (!$host || !$username || !$password || !$database) {
    http_response_code(500);
    exit('Database environment variables are not configured.');
}

try {
    $conn = new mysqli($host, $username, $password, $database, $port);
    $conn->set_charset('utf8mb4');
} catch (mysqli_sql_exception $exception) {
    http_response_code(500);
    exit('Database connection failed.');
}
