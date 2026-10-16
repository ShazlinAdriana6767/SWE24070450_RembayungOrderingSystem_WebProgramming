<?php
require 'helpers.php';
require 'db.php';
require_staff();

if ($_SERVER["REQUEST_METHOD"] !== "POST") redirect('view_orders.php');
csrf_check();

$id = (int)($_POST['id'] ?? 0);

mysqli_begin_transaction($conn);
try {
    $s1 = mysqli_prepare($conn, "DELETE FROM order_items WHERE order_id = ?");
    mysqli_stmt_bind_param($s1, "i", $id);
    mysqli_stmt_execute($s1);
    $s2 = mysqli_prepare($conn, "DELETE FROM orders WHERE order_id = ?");
    mysqli_stmt_bind_param($s2, "i", $id);
    mysqli_stmt_execute($s2);
    mysqli_commit($conn);
} catch (mysqli_sql_exception $ex) {
    mysqli_rollback($conn);
}
redirect('view_orders.php');
