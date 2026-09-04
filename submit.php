<?php
require __DIR__ . '/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

verify_csrf();

$name = trim((string)($_POST['customer_name'] ?? ''));
$email = trim((string)($_POST['customer_email'] ?? ''));
$cruiseLine = trim((string)($_POST['cruise_line'] ?? ''));
$tripName = trim((string)($_POST['trip_name'] ?? ''));
$rating = (int)($_POST['rating'] ?? 0);
$title = trim((string)($_POST['title'] ?? ''));
$reviewText = trim((string)($_POST['review_text'] ?? ''));
$consent = ($_POST['consent'] ?? '') === '1';
$formStartedAt = (int)($_POST['form_started_at'] ?? 0);

$errors = [];
if ($name === '' || mb_strlen($name) > 120) $errors[] = 'Please enter a valid name.';
if (!filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 190) $errors[] = 'Please enter a valid email address.';
if ($rating < 1 || $rating > 5) $errors[] = 'Please select a rating.';
if ($title === '' || mb_strlen($title) > 180) $errors[] = 'Please enter a review title.';
if (mb_strlen($reviewText) < 20 || mb_strlen($reviewText) > 3000) $errors[] = 'Your review must be between 20 and 3,000 characters.';
if (mb_strlen($cruiseLine) > 120) $errors[] = 'The cruise line is too long.';
if (mb_strlen($tripName) > 180) $errors[] = 'The trip or destination is too long.';
if (!$consent) $errors[] = 'Please confirm that the review is genuine and may be published.';
if (!empty($_POST['website']) || $formStartedAt < 1 || time() - $formStartedAt < 2 || time() - $formStartedAt > 86400) {
    $errors[] = 'The form session expired. Please try again.';
}
if (!empty($_SESSION['last_review_at']) && time() - (int)$_SESSION['last_review_at'] < 60) {
    $errors[] = 'Please wait a minute before submitting another review.';
}

$photoPath = null;
if (!empty($_FILES['photo']['name'])) {
    if ($_FILES['photo']['error'] !== UPLOAD_ERR_OK) {
        $errors[] = 'The photo could not be uploaded.';
    } elseif ($_FILES['photo']['size'] > 3 * 1024 * 1024) {
        $errors[] = 'The photo must be smaller than 3 MB.';
    } else {
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($_FILES['photo']['tmp_name']);
        $extensions = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
        if (!isset($extensions[$mime])) {
            $errors[] = 'Only JPG, PNG, and WebP photos are accepted.';
        } else {
            $uploadDir = __DIR__ . '/uploads';
            if (!is_dir($uploadDir) && !mkdir($uploadDir, 0755, true)) {
                $errors[] = 'The upload directory is unavailable.';
            } else {
                $filename = bin2hex(random_bytes(16)) . '.' . $extensions[$mime];
                if (move_uploaded_file($_FILES['photo']['tmp_name'], $uploadDir . '/' . $filename)) {
                    $photoPath = 'uploads/' . $filename;
                } else {
                    $errors[] = 'The photo could not be saved.';
                }
            }
        }
    }
}

if ($errors) {
    if ($photoPath !== null) @unlink(__DIR__ . '/' . $photoPath);
    flash('review_errors', $errors);
    flash('review_old', [
        'customer_name' => $name, 'customer_email' => $email, 'cruise_line' => $cruiseLine,
        'trip_name' => $tripName, 'rating' => $rating, 'title' => $title,
        'review_text' => $reviewText, 'consent' => $consent,
    ]);
    redirect('index.php#write-review');
}

$stmt = db()->prepare('INSERT INTO reviews (customer_name, customer_email, cruise_line, trip_name, rating, title, review_text, photo_path, consented_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())');
try {
    $stmt->execute([$name, $email, $cruiseLine ?: null, $tripName ?: null, $rating, $title, $reviewText, $photoPath]);
} catch (Throwable $exception) {
    if ($photoPath !== null) @unlink(__DIR__ . '/' . $photoPath);
    throw $exception;
}

$_SESSION['last_review_at'] = time();
unset($_SESSION['csrf']);

redirect('index.php?submitted=1#write-review');
