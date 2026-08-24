<?php
/**
 * PENNEN Admin — Delete Product Handler
 */
require_once __DIR__ . '/includes/auth.php';
requireAdminAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: products.php');
    exit;
}

$csrf = $_POST['csrf_token'] ?? '';
if (!verifyCsrfToken($csrf)) {
    $_SESSION['flash_error'] = 'Invalid security token for deletion.';
    header('Location: products.php');
    exit;
}

$id = trim($_POST['id'] ?? '');
if (empty($id)) {
    $_SESSION['flash_error'] = 'Invalid product ID.';
    header('Location: products.php');
    exit;
}

$db = PennenDB::getInstance();
$product = $db->getProductById($id);
$name = $product['name'] ?? $id;

if ($db->deleteProduct($id)) {
    $_SESSION['flash_success'] = "Product '{$name}' (SKU: {$id}) was removed and catalogue synchronized.";
} else {
    $_SESSION['flash_error'] = "Failed to delete product '{$name}'.";
}

header('Location: products.php');
exit;
