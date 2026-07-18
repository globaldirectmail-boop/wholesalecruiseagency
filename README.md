# Wholesale Cruise Agency Reviews

A responsive PHP/MySQL customer-review website for **www.wholesalecruiseagencyreviews.com**.

## Included

- Public review page with responsive design
- Customer review submission form
- Optional JPG, PNG, or WebP customer photo upload
- Pending-review moderation workflow
- Password-protected administrator dashboard
- Approve, unpublish, feature, unfeature, and delete controls
- CSRF protection, prepared SQL statements, output escaping, upload validation
- SEO title, description, canonical URL, and mobile-friendly markup

## Server requirements

- PHP 8.0 or newer
- MySQL 5.7+ or MariaDB 10.3+
- PHP extensions: PDO MySQL, Fileinfo, Mbstring
- Apache with `.htaccess` support, or equivalent Nginx configuration

## Installation

1. Upload all files to the domain's document root.
2. Create a MySQL database and import `database.sql`.
3. Configure these environment variables in your host control panel:

```text
DB_HOST=localhost
DB_NAME=your_database_name
DB_USER=your_database_user
DB_PASS=your_database_password
ADMIN_PASSWORD=choose-a-strong-admin-password
```

4. Ensure the `uploads` directory is writable by PHP, normally permission `755` or `775` depending on the host.
5. Open the website and submit a test review.
6. Sign in at `/admin/login.php` and approve the test review.

## Important security step

The application contains a temporary fallback administrator password of:

```text
change-this-password
```

Set the `ADMIN_PASSWORD` environment variable before making the site public. Do not use the fallback password in production.

## Main files

- `index.php` — public website and review form
- `submit.php` — review and photo submission processing
- `config.php` — database connection, sessions, CSRF, and helpers
- `database.sql` — MySQL table and sample approved reviews
- `admin/login.php` — administrator sign-in
- `admin/index.php` — moderation dashboard
- `assets/style.css` — responsive website styling

## Branding

The header currently uses a clean WCA text emblem so the site works immediately. A final logo image can be added later and referenced from `index.php`.
