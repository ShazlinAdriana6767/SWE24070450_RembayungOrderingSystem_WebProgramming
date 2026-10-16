<?php
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params(['httponly' => true, 'samesite' => 'Lax']);
    session_start();
}

function e($s): string { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function money($n): string { return 'RM ' . number_format((float)$n, 2); }
function redirect(string $url): void { header("Location: $url"); exit(); }

function is_logged_in(): bool { return isset($_SESSION['username']); }

function is_staff(): bool {
    if (!is_logged_in()) return false;
    $legacy = ['admin123', 'staff001', 'manager1', 'waiter01']; // accounts from the original site
    return in_array($_SESSION['role'] ?? '', ['admin', 'staff'], true)
        || in_array($_SESSION['username'], $legacy, true);
}

function require_login(): void { if (!is_logged_in()) redirect('login.php'); }

function require_staff(): void {
    require_login();
    if (!is_staff()) {
        http_response_code(403);
        page_header('Access denied');
        echo '<div class="card narrow center"><h2>Staff only</h2><p class="muted">You don\'t have permission to view this page.</p><p><a class="btn" href="homepage.php">Back to home</a></p></div>';
        page_footer();
        exit();
    }
}

// ---- CSRF ----
function csrf_token(): string {
    if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf'];
}
function csrf_field(): string { return '<input type="hidden" name="csrf" value="' . e(csrf_token()) . '">'; }
function csrf_check(): void {
    $ok = isset($_POST['csrf'], $_SESSION['csrf']) && hash_equals($_SESSION['csrf'], $_POST['csrf']);
    if (!$ok) { http_response_code(400); die('Invalid request. Please go back, refresh the page and try again.'); }
}

// ---- Layout ----
function page_header(string $title, string $active = '', bool $nav = true): void {
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($title) ?> · Rembayung</title>
<link rel="stylesheet" href="assets/style.css">
</head>
<body>
<?php if ($nav && is_logged_in()): ?>
<header class="topbar">
  <a class="brand" href="homepage.php">REMBAYUNG</a>
  <nav>
    <a href="homepage.php" class="<?= $active === 'home' ? 'on' : '' ?>">Home</a>
    <a href="ordercart.php" class="<?= $active === 'order' ? 'on' : '' ?>">Order</a>
    <?php if (is_staff()): ?><a href="view_orders.php" class="<?= $active === 'orders' ? 'on' : '' ?>">Orders</a><?php endif; ?>
  </nav>
  <div class="user"><span><?= e($_SESSION['username']) ?></span><a class="btn small ghost" href="logout.php">Logout</a></div>
</header>
<?php endif; ?>
<main class="wrap">
<?php
}

function page_footer(): void {
    ?>
</main>
<footer class="footer">
  <a href="https://www.instagram.com/rembayungmy" target="_blank" rel="noopener">Instagram</a>
  <span>·</span>
  <a href="https://www.tiktok.com/@rembayungmy" target="_blank" rel="noopener">TikTok</a>
  <p>© <?= date('Y') ?> Rembayung</p>
</footer>
</body>
</html>
<?php
}
