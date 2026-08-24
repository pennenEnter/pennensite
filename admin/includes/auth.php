<?php
/**
 * PENNEN Admin — Authentication Guard & Secure Upload Helpers
 */
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../db.php';

function startAdminSession(): void {
    if (session_status() === PHP_SESSION_NONE) {
        session_name(ADMIN_SESSION_NAME);
        session_start([
            'cookie_httponly' => true,
            'cookie_samesite' => 'Lax'
        ]);
    }
}

function isLoggedIn(): bool {
    startAdminSession();
    return !empty($_SESSION['admin_logged_in']) && !empty($_SESSION['admin_user']);
}

function requireAdminAuth(): void {
    startAdminSession();
    if (!isLoggedIn()) {
        header('Location: login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
        exit;
    }
}

function getAdminUser(): array {
    startAdminSession();
    return $_SESSION['admin_user'] ?? [
        'username' => 'Admin',
        'name' => 'PENNEN Brand Manager'
    ];
}

function generateCsrfToken(): string {
    startAdminSession();
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCsrfToken(?string $token): bool {
    startAdminSession();
    if (empty($token) || empty($_SESSION['csrf_token'])) return false;
    return hash_equals($_SESSION['csrf_token'], $token);
}

function sanitizeInput(?string $data): string {
    if ($data === null) return '';
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

/**
 * Validates and saves an uploaded image securely.
 *
 * @param array  $file      $_FILES element
 * @param string $productId Target product ID / SKU
 * @param string $role      e.g. 'main', 'hover', 'gallery-1', 'gallery-2'
 * @return string|false     Relative file path on success, false on failure
 */
function handleImageUpload(array $file, string $productId, string $role): string|false {
    if (empty($file['tmp_name']) || $file['error'] !== UPLOAD_ERR_OK) {
        return false;
    }

    // 1. Check file size
    if ($file['size'] > MAX_UPLOAD_SIZE) {
        return false;
    }

    // 2. Validate MIME type using finfo
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->file($file['tmp_name']);
    if (!in_array($mimeType, ALLOWED_IMAGE_TYPES, true)) {
        return false;
    }

    // 3. Validate image dimensions using getimagesize
    $imgInfo = @getimagesize($file['tmp_name']);
    if ($imgInfo === false || empty($imgInfo[0]) || empty($imgInfo[1])) {
        return false;
    }

    // 4. Validate extension
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if ($ext === 'jpeg') $ext = 'jpg';
    if (!in_array($ext, ALLOWED_IMAGE_EXTENSIONS, true)) {
        return false;
    }

    // 5. Sanitize product ID for folder creation
    $cleanId = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $productId);
    $targetDir = UPLOAD_DIR . '/' . $cleanId;
    if (!is_dir($targetDir)) {
        if (!mkdir($targetDir, 0755, true)) {
            return false;
        }
    }

    // 6. Generate safe filename
    $safeFilename = preg_replace('/[^a-zA-Z0-9_\-]/', '', $role) . '.' . $ext;
    $targetPath = $targetDir . '/' . $safeFilename;

    // 7. Move file safely
    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        return UPLOAD_URL . '/' . $cleanId . '/' . $safeFilename;
    }

    return false;
}
