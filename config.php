<?php

/**
 * PENNEN Footwear — System Configuration & Environment Settings
 */

// Database Credentials (Configure for your MySQL server)
// Database Credentials
define('DB_HOST', getenv('DB_HOST') ?: '127.0.0.1');
define('DB_PORT', getenv('DB_PORT') ?: '3307');
define('DB_NAME', getenv('DB_NAME') ?: 'pennon');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');
// Site Paths
define('SITE_NAME', 'PENNEN Footwear');
define('SITE_ROOT', __DIR__);
define('UPLOAD_DIR', __DIR__ . '/uploads/products');
define('UPLOAD_URL', 'uploads/products');

// Security & Session
define('ADMIN_SESSION_NAME', 'PENNEN_ADMIN_SESS');
define('MAX_UPLOAD_SIZE', 5 * 1024 * 1024); // 5 MB
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/webp']);
define('ALLOWED_IMAGE_EXTENSIONS', ['jpg', 'jpeg', 'png', 'webp']);

// Default Admin Credentials (Seeded on first initialization)
define('DEFAULT_ADMIN_USER', 'support@pennen.com');
define('DEFAULT_ADMIN_PASS', 'PennenAdmin2026!');
define('DEFAULT_ADMIN_NAME', 'PENNEN Brand Manager');

// Ensure upload directory exists
if (!is_dir(UPLOAD_DIR)) {
    @mkdir(UPLOAD_DIR, 0755, true);
}
