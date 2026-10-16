<?php
require 'helpers.php';
require 'db.php';
require_staff();

$q = trim($_GET['q'] ?? '');

if ($q !== '') {
    $like = '%' . $q . '%';
    $stmt = mysqli_prepare($conn, "SELECT * FROM orders WHERE customer_name LIKE ? OR table_number LIKE ? ORDER BY order_id DESC");
    mysqli_stmt_bind_param($stmt, "ss", $like, $like);
    mysqli_stmt_execute($stmt);
    $orders = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);
} else {
    $orders = mysqli_fetch_all(mysqli_query($conn, "SELECT * FROM orders ORDER BY order_id DESC"), MYSQLI_ASSOC);
}

// Load all items for the listed orders in one query
$itemsBy = [];
if ($orders) {
    $ids = array_map('intval', array_column($orders, 'order_id'));
    $res = mysqli_query($conn, "SELECT * FROM order_items WHERE order_id IN (" . implode(',', $ids) . ")");
    foreach (mysqli_fetch_all($res, MYSQLI_ASSOC) as $it) $itemsBy[$it['order_id']][] = $it;
}
$revenue = array_sum(array_column($orders, 'total_price'));

page_header('All orders', 'orders');
?>
<h1>All Orders</h1>

<div class="stats">
  <div class="stat"><span class="muted"><?= $q !== '' ? 'Matching orders' : 'Total orders' ?></span><b><?= count($orders) ?></b></div>
  <div class="stat"><span class="muted">Sales</span><b><?= money($revenue) ?></b></div>
</div>

<form class="toolbar" method="GET" action="view_orders.php">
  <input type="search" name="q" value="<?= e($q) ?>" placeholder="Search customer name or table number">
  <button class="btn" type="submit">Search</button>
  <?php if ($q !== ''): ?><a class="btn ghost" href="view_orders.php">Clear</a><?php endif; ?>
</form>

<div class="table-wrap">
<table class="data">
  <thead><tr><th>ID</th><th>Table</th><th>Customer</th><th>Items</th><th class="right">Total</th><th>Date</th><th>Action</th></tr></thead>
  <tbody>
  <?php if (!$orders): ?>
    <tr><td colspan="7" class="center muted">No orders found.</td></tr>
  <?php endif; ?>
  <?php foreach ($orders as $o): $its = $itemsBy[$o['order_id']] ?? []; ?>
    <tr>
      <td>#<?= (int)$o['order_id'] ?></td>
      <td><?= e($o['table_number']) ?></td>
      <td><?= e($o['customer_name']) ?></td>
      <td>
        <details><summary><?= count($its) ?> item(s)</summary>
          <ul><?php foreach ($its as $it): ?><li><?= (int)$it['quantity'] ?> × <?= e($it['item_name']) ?></li><?php endforeach; ?></ul>
        </details>
      </td>
      <td class="right"><?= money($o['total_price']) ?></td>
      <td><?= e($o['order_date']) ?></td>
      <td>
        <a class="btn small ghost" href="edit_order.php?id=<?= (int)$o['order_id'] ?>">Edit</a>
        <form class="inline" method="POST" action="delete_order.php" onsubmit="return confirm('Delete this order? This cannot be undone.')">
          <?= csrf_field() ?>
          <input type="hidden" name="id" value="<?= (int)$o['order_id'] ?>">
          <button class="btn small danger" type="submit">Delete</button>
        </form>
      </td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>
</div>
<?php page_footer(); ?>
