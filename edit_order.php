<?php
require 'helpers.php';
require 'db.php';
require_staff();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    csrf_check();
    $id            = (int)($_POST['order_id'] ?? 0);
    $table_number  = trim($_POST['table_number'] ?? '');
    $customer_name = trim($_POST['customer_name'] ?? '');

    if ($id > 0 && $table_number !== '' && $customer_name !== '' && strlen($table_number) <= 10 && strlen($customer_name) <= 60) {
        $stmt = mysqli_prepare($conn, "UPDATE orders SET table_number = ?, customer_name = ? WHERE order_id = ?");
        mysqli_stmt_bind_param($stmt, "ssi", $table_number, $customer_name, $id);
        mysqli_stmt_execute($stmt);
    }
    redirect('view_orders.php');
}

$id = (int)($_GET['id'] ?? 0);
$stmt = mysqli_prepare($conn, "SELECT * FROM orders WHERE order_id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
if (!$row) redirect('view_orders.php');

$st2 = mysqli_prepare($conn, "SELECT * FROM order_items WHERE order_id = ?");
mysqli_stmt_bind_param($st2, "i", $id);
mysqli_stmt_execute($st2);
$items = mysqli_fetch_all(mysqli_stmt_get_result($st2), MYSQLI_ASSOC);

page_header('Edit order', 'orders');
?>
<p><a href="view_orders.php">← Back to all orders</a></p>
<div class="card" style="max-width:560px;margin:0 auto">
  <h2>Edit Order #<?= (int)$row['order_id'] ?></h2>
  <form method="POST" action="edit_order.php">
    <?= csrf_field() ?>
    <input type="hidden" name="order_id" value="<?= (int)$row['order_id'] ?>">
    <label for="tn">Table number</label>
    <input id="tn" type="text" name="table_number" maxlength="10" required value="<?= e($row['table_number']) ?>">
    <label for="cn">Customer name</label>
    <input id="cn" type="text" name="customer_name" maxlength="60" required value="<?= e($row['customer_name']) ?>">

    <label>Items</label>
    <ul class="muted" style="margin:0;padding-left:18px">
      <?php foreach ($items as $it): ?><li><?= (int)$it['quantity'] ?> × <?= e($it['item_name']) ?> — <?= money($it['quantity'] * $it['price']) ?></li><?php endforeach; ?>
    </ul>
    <div class="total"><span>Total</span><span><?= money($row['total_price']) ?></span></div>

    <button class="btn block" type="submit">Update Order</button>
  </form>
</div>
<?php page_footer(); ?>
