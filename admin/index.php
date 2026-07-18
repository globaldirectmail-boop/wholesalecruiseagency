<?php
require dirname(__DIR__) . '/config.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $id = (int)($_POST['id'] ?? 0);
    $action = (string)($_POST['action'] ?? '');

    if ($id > 0) {
        if ($action === 'approve') {
            db()->prepare("UPDATE reviews SET status='approved' WHERE id=?")->execute([$id]);
        } elseif ($action === 'unapprove') {
            db()->prepare("UPDATE reviews SET status='pending', featured=0 WHERE id=?")->execute([$id]);
        } elseif ($action === 'feature') {
            db()->prepare("UPDATE reviews SET featured=1, status='approved' WHERE id=?")->execute([$id]);
        } elseif ($action === 'unfeature') {
            db()->prepare("UPDATE reviews SET featured=0 WHERE id=?")->execute([$id]);
        } elseif ($action === 'delete') {
            $stmt = db()->prepare('SELECT photo_path FROM reviews WHERE id=?');
            $stmt->execute([$id]);
            $review = $stmt->fetch();
            db()->prepare('DELETE FROM reviews WHERE id=?')->execute([$id]);
            if ($review && !empty($review['photo_path'])) {
                $path = dirname(__DIR__) . '/' . $review['photo_path'];
                if (is_file($path)) unlink($path);
            }
        }
    }
    header('Location: index.php');
    exit;
}

$reviews = db()->query('SELECT * FROM reviews ORDER BY status ASC, featured DESC, created_at DESC')->fetchAll();
$pending = count(array_filter($reviews, fn($r) => $r['status'] === 'pending'));
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex,nofollow">
    <title>Review Dashboard | <?= e(SITE_NAME) ?></title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body class="admin-shell">
<header class="admin-header"><div class="container"><strong>Review Dashboard</strong><div><a href="../index.php" target="_blank">View Site</a> &nbsp; · &nbsp; <a href="logout.php">Sign Out</a></div></div></header>
<main class="container admin-main">
    <div class="section-heading"><div><span class="eyebrow">Moderation</span><h1>Customer Reviews</h1><p><?= $pending ?> pending · <?= count($reviews) ?> total</p></div></div>
    <div class="admin-card" style="overflow-x:auto">
        <table class="admin-table">
            <thead><tr><th>Customer</th><th>Review</th><th>Status</th><th>Submitted</th><th>Actions</th></tr></thead>
            <tbody>
            <?php foreach ($reviews as $review): ?>
                <tr>
                    <td><strong><?= e($review['customer_name']) ?></strong><br><small><?= e($review['customer_email']) ?></small><br><small><?= e($review['cruise_line']) ?></small></td>
                    <td><div class="stars card-stars"><?= str_repeat('★', (int)$review['rating']) ?></div><strong><?= e($review['title']) ?></strong><p><?= nl2br(e($review['review_text'])) ?></p><?php if ($review['photo_path']): ?><a href="../<?= e($review['photo_path']) ?>" target="_blank">View photo</a><?php endif; ?></td>
                    <td><span class="badge badge-<?= e($review['status']) ?>"><?= e(ucfirst($review['status'])) ?></span><?php if ($review['featured']): ?><br><span class="badge" style="background:#fff7d6;color:#854d0e;margin-top:6px">Featured</span><?php endif; ?></td>
                    <td><?= e(date('M j, Y', strtotime($review['created_at']))) ?></td>
                    <td><div class="admin-actions">
                        <?php if ($review['status'] === 'pending'): ?><form method="post"><input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>"><input type="hidden" name="id" value="<?= (int)$review['id'] ?>"><button class="button button-small" name="action" value="approve">Approve</button></form><?php else: ?><form method="post"><input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>"><input type="hidden" name="id" value="<?= (int)$review['id'] ?>"><button class="button button-small button-muted" name="action" value="unapprove">Unpublish</button></form><?php endif; ?>
                        <?php if ($review['featured']): ?><form method="post"><input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>"><input type="hidden" name="id" value="<?= (int)$review['id'] ?>"><button class="button button-small button-muted" name="action" value="unfeature">Unfeature</button></form><?php else: ?><form method="post"><input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>"><input type="hidden" name="id" value="<?= (int)$review['id'] ?>"><button class="button button-small button-secondary" name="action" value="feature">Feature</button></form><?php endif; ?>
                        <form method="post" onsubmit="return confirm('Permanently delete this review?')"><input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>"><input type="hidden" name="id" value="<?= (int)$review['id'] ?>"><button class="button button-small button-danger" name="action" value="delete">Delete</button></form>
                    </div></td>
                </tr>
            <?php endforeach; ?>
            <?php if (!$reviews): ?><tr><td colspan="5">No reviews have been submitted.</td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>
</main>
</body>
</html>
