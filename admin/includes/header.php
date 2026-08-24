<?php
/**
 * PENNEN Admin — Reusable Header Component
 */
require_once __DIR__ . '/auth.php';
requireAdminAuth();

$currentUser = getAdminUser();
$activeTab = $activeTab ?? 'dashboard';
$flashSuccess = $_SESSION['flash_success'] ?? null;
$flashError = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_success'], $_SESSION['flash_error']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo htmlspecialchars($pageTitle ?? 'Admin Panel — PENNEN Footwear'); ?></title>
  
  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Anton&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;600;700;800&display=swap" rel="stylesheet">
  
  <link rel="stylesheet" href="assets/css/admin.css">
</head>
<body>

<header class="admin-nav">
  <div class="admin-nav-inner">
    <a href="dashboard.php" class="admin-brand">
      <img src="../pennen-icon.png" alt="PENNEN" class="admin-logo">
      <span class="admin-badge">Product CMS</span>
    </a>

    <nav class="admin-menu">
      <a href="dashboard.php" class="<?php echo $activeTab === 'dashboard' ? 'active' : ''; ?>">Dashboard</a>
      <a href="products.php" class="<?php echo $activeTab === 'products' ? 'active' : ''; ?>">All Products</a>
      <a href="product-add.php" class="<?php echo $activeTab === 'product-add' ? 'active' : ''; ?>">+ Add Product</a>
      <a href="../index.php" target="_blank" rel="noopener">View Public Site ↗</a>
    </nav>

    <div class="admin-user-pill">
      <span class="admin-user-name"><?php echo htmlspecialchars($currentUser['name'] ?? 'Admin'); ?></span>
      <a href="logout.php" class="btn-admin btn-admin-ghost" title="Sign out of admin session">Logout</a>
    </div>
  </div>
</header>

<main class="admin-container">
  <?php if ($flashSuccess): ?>
    <div class="alert alert-success">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
      <span><?php echo htmlspecialchars($flashSuccess); ?></span>
    </div>
  <?php endif; ?>

  <?php if ($flashError): ?>
    <div class="alert alert-error">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
      <span><?php echo htmlspecialchars($flashError); ?></span>
    </div>
  <?php endif; ?>
