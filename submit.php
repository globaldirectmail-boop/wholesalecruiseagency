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

$errors = [];
if ($name === '' || mb_strlen($name) > 120) $errors[] = 'Please enter a valid name.';
if (!filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 190) $errors[] = 'Please enter a valid email address.';
if ($rating < 1 || $rating > 5) $errors[] = 'Please select a rating.';
if ($title === '' || mb_strlen($title) > 180) $errors[] = 'Please enter a review title.';
if (mb_strlen($reviewText) < 20 || mb_strlen($reviewText) > 3000) $errors[] = 'Your review must be between 20 and 3,000 characters.';

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
    http_response_code(422);
    echo '<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><link rel="stylesheet" href="assets/style.css"><title>Review Error</title></head><body><div class="container error-page"><h1>Please correct the following</h1><ul>';
    foreach ($errors as $error) echo '<li>' . e($error) . '</li>';
    echo '</ul><a class="button" href="index.php#write-review">Return to the form</a></div></body></html>';
    exit;
}

$stmt = db()->prepare('INSERT INTO reviews (customer_name, customer_email, cruise_line, trip_name, rating, title, review_text, photo_path) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
$stmt->execute([$name, $email, $cruiseLine ?: null, $tripName ?: null, $rating, $title, $reviewText, $photoPath]);

header('Location: index.php?submitted=1#write-review');
exit;
