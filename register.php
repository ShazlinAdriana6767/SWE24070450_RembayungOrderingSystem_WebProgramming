<?php
require 'helpers.php';
require 'db.php';

if (is_logged_in()) redirect('homepage.php');

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    csrf_check();
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm'] ?? '';

    if (!preg_match('/^[A-Za-z0-9_.]{7,30}$/', $username)) {
        $error = "Username must be 7-30 characters: letters, numbers, _ or . only.";
    } elseif (strlen($password) < 7) {
        $error = "Password must be at least 7 characters.";
    } elseif ($password !== $confirm) {
        $error = "Passwords do not match.";
    } else {
        $stmt = mysqli_prepare($conn, "SELECT 1 FROM users WHERE username = ?");
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        if (mysqli_fetch_row(mysqli_stmt_get_result($stmt))) {
            $error = "That username is already taken.";
        } else {
            // Public sign-up is always a customer. Staff/admin roles are set by the owner in the database.
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $role = 'customer';
            $ins = mysqli_prepare($conn, "INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
            mysqli_stmt_bind_param($ins, "sss", $username, $hash, $role);
            mysqli_stmt_execute($ins);
            redirect('login.php?registered=1');
        }
    }
}

page_header('Register', '', false);
?>
<div class="card narrow">
  <h1 class="auth-brand">REMBAYUNG</h1>
  <p class="center muted">Create your account</p>
  <?php if ($error): ?><div class="alert err"><?= e($error) ?></div><?php endif; ?>
  <form method="POST" action="register.php">
    <?= csrf_field() ?>
    <label for="u">Username</label>
    <input id="u" type="text" name="username" required minlength="7" maxlength="30" autocomplete="username" value="<?= e($_POST['username'] ?? '') ?>">
    <label for="p">Password</label>
    <input id="p" type="password" name="password" required minlength="7" autocomplete="new-password">
    <label for="c">Confirm password</label>
    <input id="c" type="password" name="confirm" required minlength="7" autocomplete="new-password">
    <p><button class="btn block" type="submit">Create account</button></p>
  </form>
  <p class="center muted"><a href="login.php">Back to login</a></p>
</div>
<?php page_footer(); ?>
