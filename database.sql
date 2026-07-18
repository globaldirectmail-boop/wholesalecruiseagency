CREATE TABLE IF NOT EXISTS reviews (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    customer_name VARCHAR(120) NOT NULL,
    customer_email VARCHAR(190) NOT NULL,
    cruise_line VARCHAR(120) DEFAULT NULL,
    trip_name VARCHAR(180) DEFAULT NULL,
    rating TINYINT UNSIGNED NOT NULL,
    title VARCHAR(180) NOT NULL,
    review_text TEXT NOT NULL,
    photo_path VARCHAR(255) DEFAULT NULL,
    status ENUM('pending','approved') NOT NULL DEFAULT 'pending',
    featured TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_status_created (status, created_at),
    INDEX idx_featured (featured)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO reviews
(customer_name, customer_email, cruise_line, trip_name, rating, title, review_text, status, featured)
VALUES
('Sarah M.', 'sample@example.com', 'Viking Ocean', 'Mediterranean Cruise', 5, 'Exceptional service from start to finish', 'Our advisor made the entire booking process simple and helped us find a wonderful itinerary at a price we were very happy with.', 'approved', 1),
('Michael R.', 'sample2@example.com', 'Royal Caribbean', 'Caribbean Getaway', 5, 'A smooth and stress-free experience', 'Every question was answered quickly and professionally. We will absolutely use Wholesale Cruise Agency again.', 'approved', 1);
