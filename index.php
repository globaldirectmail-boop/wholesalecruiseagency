<?php
require __DIR__ . '/config.php';

$reviews = db()->query("SELECT customer_name, cruise_line, trip_name, rating, title, review_text, photo_path, created_at FROM reviews WHERE status = 'approved' ORDER BY featured DESC, created_at DESC LIMIT 24")->fetchAll();
$submitted = isset($_GET['submitted']);
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Customer Reviews | Wholesale Cruise Agency</title>
    <meta name="description" content="Read verified customer reviews and share your experience with Wholesale Cruise Agency.">
    <link rel="canonical" href="<?= SITE_URL ?>">
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<header class="site-header">
    <div class="container nav-wrap">
        <a class="brand" href="#top" aria-label="Wholesale Cruise Agency Reviews home">
            <span class="brand-mark">WCA</span>
            <span><strong>Wholesale Cruise Agency</strong><small>Customer Reviews</small></span>
        </a>
        <nav><a href="#reviews">Reviews</a><a href="#write-review" class="button button-small">Write a Review</a></nav>
    </div>
</header>

<main id="top">
    <section class="hero">
        <div class="container hero-grid">
            <div>
                <span class="eyebrow">Real travelers. Real experiences.</span>
                <h1>See why travelers choose Wholesale Cruise Agency</h1>
                <p>Read customer stories, discover memorable cruise experiences, and share your own journey with our team.</p>
                <div class="hero-actions"><a class="button" href="#reviews">Read Reviews</a><a class="button button-secondary" href="#write-review">Share Your Experience</a></div>
            </div>
            <div class="rating-panel" aria-label="Customer rating summary">
                <div class="big-rating">5.0</div>
                <div class="stars">★★★★★</div>
                <p>Based on approved customer reviews</p>
                <div class="trust-row"><span>✓ Personal service</span><span>✓ Cruise expertise</span><span>✓ Wholesale value</span></div>
            </div>
        </div>
    </section>

    <?php if ($submitted): ?>
        <div class="container"><div class="notice success">Thank you! Your review was received and will appear after approval.</div></div>
    <?php endif; ?>

    <section id="reviews" class="section">
        <div class="container">
            <div class="section-heading"><div><span class="eyebrow">Traveler stories</span><h2>What our customers are saying</h2></div><a href="#write-review">Leave a review →</a></div>
            <div class="review-grid">
                <?php if (!$reviews): ?>
                    <div class="empty-state">No reviews have been published yet. Be the first to share your experience.</div>
                <?php endif; ?>
                <?php foreach ($reviews as $review): ?>
                    <article class="review-card">
                        <div class="review-top">
                            <?php if (!empty($review['photo_path'])): ?>
                                <img class="avatar" src="<?= e($review['photo_path']) ?>" alt="Customer photo" loading="lazy">
                            <?php else: ?>
                                <div class="avatar avatar-placeholder"><?= e(strtoupper(substr($review['customer_name'], 0, 1))) ?></div>
                            <?php endif; ?>
                            <div><strong><?= e($review['customer_name']) ?></strong><small><?= e($review['cruise_line'] ?: 'Cruise traveler') ?></small></div>
                            <div class="stars card-stars"><?= str_repeat('★', (int)$review['rating']) ?></div>
                        </div>
                        <h3><?= e($review['title']) ?></h3>
                        <p><?= nl2br(e($review['review_text'])) ?></p>
                        <?php if ($review['trip_name']): ?><div class="trip-tag"><?= e($review['trip_name']) ?></div><?php endif; ?>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section id="write-review" class="section form-section">
        <div class="container form-grid">
            <div>
                <span class="eyebrow">Your experience matters</span>
                <h2>Share your cruise story</h2>
                <p>Tell future travelers about your booking experience, your advisor, and your favorite part of the trip.</p>
                <div class="privacy-note">Your email address is used only for review verification and is never displayed publicly.</div>
            </div>
            <form action="submit.php" method="post" enctype="multipart/form-data" class="review-form">
                <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                <div class="field-row">
                    <label>Full name<input name="customer_name" required maxlength="120" autocomplete="name"></label>
                    <label>Email address<input type="email" name="customer_email" required maxlength="190" autocomplete="email"></label>
                </div>
                <div class="field-row">
                    <label>Cruise line<input name="cruise_line" maxlength="120" placeholder="Viking, Royal Caribbean, etc."></label>
                    <label>Trip or destination<input name="trip_name" maxlength="180" placeholder="Alaska, Mediterranean, Caribbean..."></label>
                </div>
                <fieldset class="rating-field"><legend>Your rating</legend><div class="rating-options">
                    <?php for ($i = 5; $i >= 1; $i--): ?><label><input type="radio" name="rating" value="<?= $i ?>" <?= $i === 5 ? 'checked' : '' ?>><span><?= $i ?> ★</span></label><?php endfor; ?>
                </div></fieldset>
                <label>Review title<input name="title" required maxlength="180" placeholder="Summarize your experience"></label>
                <label>Your review<textarea name="review_text" required minlength="20" maxlength="3000" rows="6" placeholder="What stood out? How did our team help?"></textarea></label>
                <label>Photo <span class="optional">(optional, JPG/PNG/WebP up to 3 MB)</span><input type="file" name="photo" accept="image/jpeg,image/png,image/webp"></label>
                <label class="check"><input type="checkbox" required><span>I confirm this review reflects my genuine experience.</span></label>
                <button class="button" type="submit">Submit Review</button>
            </form>
        </div>
    </section>
</main>

<footer><div class="container footer-wrap"><div><strong>Wholesale Cruise Agency</strong><p>Helping travelers cruise with confidence and exceptional value.</p></div><div><a href="https://www.wholesalecruiseagency.com">Visit our main website</a><a href="admin/login.php">Admin</a></div></div></footer>
<script src="assets/script.js"></script>
</body>
</html>
