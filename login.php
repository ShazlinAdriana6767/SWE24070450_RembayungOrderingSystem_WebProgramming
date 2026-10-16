<?php
require 'helpers.php';
require 'db.php';

if (is_logged_in()) redirect('homepage.php');

$error = "";
$notice = isset($_GET['registered']) ? "Account created. You can log in now." : "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    csrf_check();
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = mysqli_prepare($conn, "SELECT username, password, role FROM users WHERE username = ?");
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

    $valid = false;
    if ($user) {
        if (password_verify($password, $user['password'])) {
            $valid = true;
        } elseif (hash_equals($user['password'], $password)) {
            // Old account with a plain-text password: accept it once, then store a hash.
            // (Needs the column change in upgrade.sql so the hash fits.)
            $valid = true;
            $len = mysqli_fetch_row(mysqli_query($conn,
                "SELECT CHARACTER_MAXIMUM_LENGTH FROM INFORMATION_SCHEMA.COLUMNS
                 WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'password'"))[0] ?? 0;
            if ((int)$len >= 60) {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $up = mysqli_prepare($conn, "UPDATE users SET password = ? WHERE username = ?");
                mysqli_stmt_bind_param($up, "ss", $hash, $user['username']);
                mysqli_stmt_execute($up);
            }
        }
    }

    if ($valid) {
        session_regenerate_id(true);
        $_SESSION['username'] = $user['username'];
        $_SESSION['role']     = $user['role'];
        redirect('homepage.php');
    }
    usleep(300000);
    $error = "Invalid username or password.";
}

page_header('Login', '', false);
?>
<div class="card narrow">
  <h1 class="auth-brand">REMBAYUNG</h1>
  <p class="center muted">Sign in to place or manage orders</p>
  <?php if ($notice): ?><div class="alert ok"><?= e($notice) ?></div><?php endif; ?>
  <?php if ($error): ?><div class="alert err"><?= e($error) ?></div><?php endif; ?>
  <form method="POST" action="login.php">
    <?= csrf_field() ?>
    <label for="u">Username</label>
    <input id="u" type="text" name="username" required minlength="7" autocomplete="username" autofocus value="<?= e($_POST['username'] ?? '') ?>">
    <label for="p">Password</label>
    <input id="p" type="password" name="password" required minlength="7" autocomplete="current-password">
    <p><button class="btn block" type="submit">Login</button></p>
  </form>
  <p class="center muted">No account? <a href="register.php">Register here</a></p>
</div>
<?php page_footer(); ?>
