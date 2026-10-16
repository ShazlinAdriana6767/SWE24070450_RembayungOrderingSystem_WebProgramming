<?php
require 'helpers.php';
require 'menu.php';
require_login();

$flat = menu_lookup($MENU);
$error = $_SESSION['order_error'] ?? '';
unset($_SESSION['order_error']);

page_header('Place an order', 'order');
?>
<h1>Place an Order</h1>
<?php if ($error): ?><div class="alert err"><?= e($error) ?></div><?php endif; ?>

<form action="save_order.php" method="POST" id="orderForm">
<?= csrf_field() ?>
<div class="order-layout">
  <div>
    <div class="chips">
      <?php foreach (array_keys($MENU) as $cat): ?><a href="#sec-<?= e($cat) ?>"><?= e($cat) ?></a><?php endforeach; ?>
    </div>

    <?php foreach ($MENU as $cat => $items): ?>
    <section class="menu-sec" id="sec-<?= e($cat) ?>">
      <h2><?= e($cat) ?></h2>
      <?php foreach ($items as $id => [$name, $price]): ?>
      <div class="item" data-id="<?= e($id) ?>" data-name="<?= e($name) ?>" data-price="<?= e($price) ?>">
        <div><div class="nm"><?= e($name) ?></div><div class="pr"><?= money($price) ?></div></div>
        <div class="qty">
          <button type="button" data-d="-1" aria-label="Less <?= e($name) ?>">−</button>
          <output>0</output>
          <button type="button" data-d="1" aria-label="More <?= e($name) ?>">+</button>
          <input type="hidden" name="qty[<?= e($id) ?>]" value="0">
        </div>
      </div>
      <?php endforeach; ?>
    </section>
    <?php endforeach; ?>
  </div>

  <aside class="card cart">
    <h2>Your order</h2>
    <label for="tn">Table number</label>
    <input id="tn" type="text" name="table_number" maxlength="10" required>
    <label for="cn">Customer name</label>
    <input id="cn" type="text" name="customer_name" maxlength="60" required>
    <ul id="lines"><li class="muted">Nothing added yet</li></ul>
    <div class="total"><span>Total</span><span id="total">RM 0.00</span></div>
    <button class="btn block" id="submitBtn" type="submit" disabled>Place Order</button>
  </aside>
</div>
</form>

<script>
(function(){
  const rows = document.querySelectorAll('.item');
  const lines = document.getElementById('lines');
  const totalEl = document.getElementById('total');
  const btn = document.getElementById('submitBtn');
  const MAX = 20;

  function render(){
    let total = 0, html = '';
    rows.forEach(r => {
      const q = +r.querySelector('input').value;
      if (q > 0) {
        const p = +r.dataset.price;
        total += p * q;
        const n = r.dataset.name.replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
        html += '<li><span>' + q + ' × ' + n + '</span><span>RM ' + (p*q).toFixed(2) + '</span></li>';
      }
    });
    lines.innerHTML = html || '<li class="muted">Nothing added yet</li>';
    totalEl.textContent = 'RM ' + total.toFixed(2);
    btn.disabled = total <= 0;
  }

  rows.forEach(r => {
    const input = r.querySelector('input'), out = r.querySelector('output');
    r.querySelectorAll('button').forEach(b => b.addEventListener('click', () => {
      const v = Math.min(MAX, Math.max(0, +input.value + +b.dataset.d));
      input.value = v; out.textContent = v; render();
    }));
  });
})();
</script>
<?php page_footer(); ?>
