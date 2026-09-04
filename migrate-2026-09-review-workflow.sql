-- Run once on databases created with the July 2026 schema.
ALTER TABLE reviews
    MODIFY status ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
    ADD COLUMN consented_at TIMESTAMP NULL DEFAULT NULL AFTER featured,
    ADD COLUMN approved_at TIMESTAMP NULL DEFAULT NULL AFTER featured,
    ADD COLUMN moderated_at TIMESTAMP NULL DEFAULT NULL AFTER approved_at;

UPDATE reviews
SET approved_at = COALESCE(approved_at, created_at)
WHERE status = 'approved';

-- Historical rows predate explicit consent capture; leave them NULL.
-- New submissions receive the current timestamp from submit.php.

CREATE TABLE review_moderation_log (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    review_id INT UNSIGNED NOT NULL,
    action VARCHAR(30) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_review_created (review_id, created_at),
    CONSTRAINT fk_moderation_review FOREIGN KEY (review_id) REFERENCES reviews(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
