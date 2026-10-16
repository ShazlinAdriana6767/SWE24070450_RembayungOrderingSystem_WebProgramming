<?php
require 'helpers.php';
require_login();

$best = [
    ['pic1.jpeg', 'Sambal Sotong Tumis Petai', 43.90],
    ['pic2.jpeg', 'Ayam Masak Kicap',          29.90],
    ['pic3.jpeg', 'Ayam Masak Lemak Cili Api', 29.90],
    ['pic4.jpeg', 'Kids Meal',                 15.90],
];

page_header('Home', 'home');
?>
<section class="hero">
  <h1>REMBAYUNG</h1>
  <p>Northern Malay home cooking, served with sambal, santan and a lot of heart.</p>
  <a class="btn" href="ordercart.php">Order Now</a>
  <?php if (is_staff()): ?> <a class="btn ghost" href="view_orders.php">View Orders</a><?php endif; ?>
</section>

<h2 class="center">Best Sellers</h2>
<div class="grid">
<?php foreach ($best as [$img, $name, $price]): ?>
  <article class="dish">
    <img src="<?= e($img) ?>" alt="<?= e($name) ?>" loading="lazy">
    <div>
      <span class="badge">BEST SELLER</span>
      <h3><?= e($name) ?></h3>
      <span class="price"><?= money($price) ?></span>
    </div>
  </article>
<?php endforeach; ?>
</div>
<?php page_footer(); ?>
