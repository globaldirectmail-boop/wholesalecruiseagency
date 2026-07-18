<?php
require dirname(__DIR__) . '/config.php';

if (is_admin()) {
    header('Location: index.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $password = (string)($_POST['password'] ?? '');
    $configuredPassword = env_value('ADMIN_PASSWORD', 'change-this-password');

    if (hash_equals($configuredPassword, $password)) {
        session_regenerate_id(true);
        $_SESSION['admin_logged_in'] = true;
        header('Location: index.php');
        exit;
    }
    $error = 'Incorrect password.';
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex,nofollow">
    <title>Admin Login | <?= e(SITE_NAME) ?></title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body class="admin-shell">
<main class="container">
    <div class="admin-card admin-login">
        <span class="eyebrow">Secure administration</span>
        <h1>Review Dashboard</h1>
        <p>Enter the administrator password to moderate customer reviews.</p>
        <?php if ($error): ?><div class="notice" style="background:#fef2f2;color:#991b1b"><?= e($error) ?></div><?php endif; ?>
        <form method="post">
            <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
            <label>Password<input type="password" name="password" required autocomplete="current-password"></label>
            <button class="button" type="submit" style="width:100%;margin-top:18px">Sign In</button>
        </form>
        <p><a href="../index.php">← Return to website</a></p>
    </div>
</main>
</body>
</html>
