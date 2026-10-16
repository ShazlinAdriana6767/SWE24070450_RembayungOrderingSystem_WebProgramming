<?php
// Database connection (same settings as before)
$host     = "localhost";
$user     = "root";
$password = "";
$database = "rembayung_db";

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = mysqli_connect($host, $user, $password, $database);
    mysqli_set_charset($conn, "utf8mb4");
} catch (mysqli_sql_exception $ex) {
    http_response_code(500);
    die("Sorry, we can't reach the database right now. Please try again shortly.");
}
