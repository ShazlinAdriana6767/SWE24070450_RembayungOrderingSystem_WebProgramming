<?php
require 'helpers.php';
require 'menu.php';
require 'db.php';
require_login();

if ($_SERVER["REQUEST_METHOD"] !== "POST") redirect('ordercart.php');
csrf_check();

function fail(string $msg): void { $_SESSION['order_error'] = $msg; redirect('ordercart.php'); }

$table_number  = trim($_POST['table_number'] ?? '');
$customer_name = trim($_POST['customer_name'] ?? '');
if ($table_number === '' || strlen($table_number) > 10) fail("Please enter a valid table number.");
if ($customer_name === '' || strlen($customer_name) > 60) fail("Please enter the customer name.");

$flat  = menu_lookup($MENU);
$lines = [];
$total = 0.0;

foreach ((array)($_POST['qty'] ?? []) as $id => $qty) {
    if (!isset($flat[$id])) continue;           // ignore anything not on the menu
    $qty = (int)$qty;
    if ($qty < 1) continue;
    $qty = min($qty, 20);
    $lines[] = ['name' => $flat[$id]['name'], 'cat' => $flat[$id]['category'], 'qty' => $qty, 'price' => $flat[$id]['price']];
    $total += $flat[$id]['price'] * $qty;       // price comes from the server, not the form
}
if (!$lines) fail("Please add at least one item to your order.");

mysqli_begin_transaction($conn);
try {
    $stmt = mysqli_prepare($conn, "INSERT INTO orders (table_number, customer_name, total_price) VALUES (?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "ssd", $table_number, $customer_name, $total);
    mysqli_stmt_execute($stmt);
    $order_id = mysqli_insert_id($conn);

    $ins = mysqli_prepare($conn, "INSERT INTO order_items (order_id, item_name, category, quantity, price) VALUES (?, ?, ?, ?, ?)");
    foreach ($lines as $l) {
        mysqli_stmt_bind_param($ins, "issid", $order_id, $l['name'], $l['cat'], $l['qty'], $l['price']);
        mysqli_stmt_execute($ins);
    }
    mysqli_commit($conn);
} catch (mysqli_sql_exception $ex) {
    mysqli_rollback($conn);
    fail("Sorry, we couldn't save your order. Please try again.");
}

$_SESSION['last_order'] = $order_id;
redirect('orderreceived.php');
