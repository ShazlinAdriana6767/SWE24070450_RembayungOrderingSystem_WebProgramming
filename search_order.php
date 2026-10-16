<?php
// Search now lives on view_orders.php; this keeps old links working.
$q = trim($_POST['search'] ?? $_GET['search'] ?? '');
header("Location: view_orders.php" . ($q !== '' ? "?q=" . urlencode($q) : ""));
exit();
