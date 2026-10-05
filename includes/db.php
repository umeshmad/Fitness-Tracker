<?php
// Database connection shared by every page. Defaults match a fresh XAMPP install.
// Throw exceptions on MySQL errors so a failed connection can be caught below.
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'fitness_tracker');

try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $conn->set_charset('utf8mb4');
} catch (mysqli_sql_exception $ex) {
    http_response_code(500);
    exit('Database connection failed. Start MySQL in XAMPP/WAMP, import database.sql, and check the settings in includes/db.php.');
}
