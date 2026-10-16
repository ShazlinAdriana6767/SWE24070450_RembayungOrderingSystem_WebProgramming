<?php
require 'helpers.php';
require 'db.php';
require_login();

// Customers see the order they just placed; staff may open any order by id.
$id = (is_staff() && isset($_GET['id'])) ? (int)$_GET['id'] : (int)($_SESSION['last_order'] ?? 0);
if ($id <= 0) redirect('ordercart.php');

$stmt = mysqli_prepare($conn, "SELECT * FROM orders WHERE order_id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$order = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
if (!$order) redirect('ordercart.php');

$st2 = mysqli_prepare($conn, "SELECT * FROM order_items WHERE order_id = ?");
mysqli_stmt_bind_param($st2, "i", $id);
mysqli_stmt_execute($st2);
$items = mysqli_fetch_all(mysqli_stmt_get_result($st2), MYSQLI_ASSOC);

page_header('Order received', 'order');
?>
<div class="center">
  <div class="check"><svg viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg></div>
  <h1>Order Received</h1>
  <p class="muted">Thank you, <?= e($order['customer_name']) ?>! We'll start preparing your order.</p>
</div>

<div class="card" style="max-width:720px;margin:20px auto">
  <p><b>Order #<?= (int)$order['order_id'] ?></b> · Table <?= e($order['table_number']) ?></p>
  <div class="table-wrap">
    <table class="data">
      <thead><tr><th>Item</th><th>Category</th><th>Qty</th><th class="right">Price</th><th class="right">Subtotal</th></tr></thead>
      <tbody>
      <?php foreach ($items as $it): ?>
        <tr>
          <td><?= e($it['item_name']) ?></td>
          <td><?= e($it['category']) ?></td>
          <td><?= (int)$it['quantity'] ?></td>
          <td class="right"><?= money($it['price']) ?></td>
          <td class="right"><?= money($it['quantity'] * $it['price']) ?></td>
        </tr>
      <?php endforeach; ?>
        <tr><td colspan="4" class="right"><b>Total</b></td><td class="right"><b><?= money($order['total_price']) ?></b></td></tr>
      </tbody>
    </table>
  </div>
  <p class="center"><a class="btn" href="homepage.php">Back to Home</a></p>
</div>
<?php page_footer(); ?>
