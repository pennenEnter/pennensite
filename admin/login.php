<?php
/**
 * PENNEN Admin — Secure Login Interface
 */
require_once __DIR__ . '/includes/auth.php';
startAdminSession();

// If already logged in, redirect to dashboard
if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit;
}

$error = null;
$redirect = $_GET['redirect'] ?? 'dashboard.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrf = $_POST['csrf_token'] ?? '';
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!verifyCsrfToken($csrf)) {
        $error = 'Invalid security token. Please try again.';
    } elseif (empty($username) || empty($password)) {
        $error = 'Please enter both username and password.';
    } else {
        $db = PennenDB::getInstance();
        $admin = $db->getAdminByUsername($username);

        if ($admin && password_verify($password, $admin['password_hash'])) {
            // Password verified successfully
            session_regenerate_id(true);
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_user'] = [
                'id' => $admin['id'],
                'username' => $admin['username'],
                'name' => $admin['name'] ?? 'Admin'
            ];
            $_SESSION['flash_success'] = 'Welcome back, ' . htmlspecialchars($admin['name'] ?? $admin['username']) . '!';
            
            // Clean redirect target
            $target = (str_starts_with($redirect, 'http') || str_contains($redirect, ':')) ? 'dashboard.php' : $redirect;
            header('Location: ' . $target);
            exit;
        } else {
            $error = 'Invalid credentials. Please verify username and password.';
        }
    }
}

$csrfToken = generateCsrfToken();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Login — PENNEN Footwear CMS</title>
  
  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Anton&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;600;700;800&display=swap" rel="stylesheet">
  
  <link rel="stylesheet" href="assets/css/admin.css">
</head>
<body>

<div class="login-wrap">
  <div class="login-card">
    <img src="../pennen-icon.png" alt="PENNEN" class="login-logo">
    <h1 class="login-title">Product CMS</h1>
    <p class="login-sub">Sign in to manage PENNEN catalogue &amp; product showcase</p>

    <?php if ($error): ?>
      <div class="alert alert-error" style="text-align:left;">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
        <span><?php echo htmlspecialchars($error); ?></span>
      </div>
    <?php endif; ?>

    <form method="POST" action="login.php<?php echo !empty($_GET['redirect']) ? '?redirect=' . urlencode($_GET['redirect']) : ''; ?>" style="text-align:left;">
      <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken); ?>">

      <div class="form-group">
        <label class="form-label" for="username">Username</label>
        <input type="text" id="username" name="username" class="form-input" required autofocus autocomplete="username" placeholder="e.g. admin" value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>">
      </div>

      <div class="form-group" style="margin-bottom: 24px;">
        <label class="form-label" for="password">Password</label>
        <input type="password" id="password" name="password" class="form-input" required autocomplete="current-password" placeholder="Enter your password">
      </div>

      <button type="submit" class="btn-admin btn-admin-gold" style="width: 100%; padding: 12px; font-size: 0.82rem;">
        Sign In to Admin Panel →
      </button>
    </form>

    <div style="margin-top: 24px; font-family: var(--font-mono); font-size: 0.68rem; color: var(--admin-muted);">
      Initial default: <code style="color:var(--admin-gold);">admin</code> / <code style="color:var(--admin-gold);">PennenAdmin2026!</code>
    </div>
  </div>
</div>

</body>
</html>
