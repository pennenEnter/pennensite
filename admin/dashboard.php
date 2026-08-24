<?php
/**
 * PENNEN Admin — Dashboard Overview
 */
$pageTitle = 'Dashboard — PENNEN Product CMS';
$activeTab = 'dashboard';

require_once __DIR__ . '/includes/header.php';

$db = PennenDB::getInstance();
$stats = $db->getDashboardStats();
$recentProducts = array_slice($db->getAllProducts(['status' => 'all']), 0, 8);
$csrfToken = generateCsrfToken();
?>

<div class="admin-page-header">
  <div>
    <div class="admin-badge" style="margin-bottom: 8px;">PENNEN Brand Discovery Showcase</div>
    <h1 class="admin-page-title">Product CMS Dashboard</h1>
    <p class="admin-page-sub">Manage product catalogue, marketplace links, and showcase visibility in real-time.</p>
  </div>

  <div style="display:flex; gap:12px; flex-wrap:wrap;">
    <a href="product-add.php" class="btn-admin btn-admin-gold">+ Add New Product</a>
    <a href="products.php" class="btn-admin btn-admin-ghost">All Products (<?php echo $stats['total']; ?>)</a>
  </div>
</div>

<!-- Metrics Cards Grid -->
<div class="stats-grid">
  <div class="stat-card">
    <span class="stat-label">Total Catalogue</span>
    <span class="stat-val"><?php echo $stats['total']; ?></span>
  </div>

  <div class="stat-card stat-green">
    <span class="stat-label">Published (Live)</span>
    <span class="stat-val"><?php echo $stats['published']; ?></span>
  </div>

  <div class="stat-card">
    <span class="stat-label">Drafts (Hidden)</span>
    <span class="stat-val"><?php echo $stats['draft']; ?></span>
  </div>

  <div class="stat-card stat-gold">
    <span class="stat-label">Featured / Bestsellers</span>
    <span class="stat-val"><?php echo $stats['featured']; ?></span>
  </div>

  <div class="stat-card stat-accent">
    <span class="stat-label">New Arrivals</span>
    <span class="stat-val"><?php echo $stats['newArrival']; ?></span>
  </div>
</div>

<!-- Recent Products Showcase Table -->
<div class="table-card">
  <div class="table-header">
    <span class="table-title">Recent Footwear Silhouettes</span>
    <a href="products.php" class="btn-admin btn-admin-ghost" style="padding: 6px 12px; font-size: 0.7rem;">View All Products →</a>
  </div>

  <?php if (empty($recentProducts)): ?>
    <div style="padding: 40px; text-align: center; color: var(--admin-muted); font-family: var(--font-mono);">
      No products found in catalogue. Click <strong>+ Add New Product</strong> above to get started.
    </div>
  <?php else: ?>
    <div style="overflow-x: auto;">
      <table class="admin-table">
        <thead>
          <tr>
            <th style="width: 70px;">Image</th>
            <th>Product Name &amp; SKU</th>
            <th>Category / Silhouette</th>
            <th>Price</th>
            <th>Badges</th>
            <th>Status</th>
            <th style="text-align: right;">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($recentProducts as $p): ?>
            <?php 
              $pubStatus = $p['publication_status'] ?? $p['status'] ?? 'published';
              $imgSrc = !empty($p['image']) ? '../' . $p['image'] : '../hero-shoe.png';
              $silhouette = $p['silhouette'] ?? $p['shape'] ?? 'sneaker';
              $ribbon = $p['sticker_ribbon'] ?? $p['sticker'] ?? 'none';
              $sku = !empty($p['sku']) ? $p['sku'] : (string)$p['id'];
            ?>
            <tr>
              <td>
                <img src="<?php echo htmlspecialchars($imgSrc); ?>" alt="<?php echo htmlspecialchars($p['name']); ?>" class="product-row-img" onerror="this.src='../hero-shoe.png'">
              </td>
              <td>
                <div class="product-row-title"><?php echo htmlspecialchars($p['name']); ?></div>
                <div class="product-row-sku">SKU: <?php echo htmlspecialchars($sku); ?></div>
              </td>
              <td>
                <div style="font-family: var(--font-mono); font-size: 0.76rem; text-transform: uppercase; color: var(--admin-text);">
                  <?php echo htmlspecialchars($p['category']); ?>
                </div>
                <div style="font-family: var(--font-mono); font-size: 0.68rem; text-transform: uppercase; color: var(--admin-muted);">
                  Silhouette: <?php echo htmlspecialchars($silhouette); ?>
                </div>
              </td>
              <td>
                <div style="font-family: var(--font-mono); font-weight: 700; color: var(--admin-text);">
                  ₹<?php echo number_format($p['price']); ?>
                </div>
                <?php if (!empty($p['mrp'])): ?>
                  <div style="font-family: var(--font-mono); font-size: 0.72rem; color: var(--admin-muted); text-decoration: line-through;">
                    MRP ₹<?php echo number_format($p['mrp']); ?>
                  </div>
                <?php endif; ?>
              </td>
              <td>
                <div style="display: flex; gap: 6px; flex-wrap: wrap;">
                  <?php if ($ribbon === 'bestseller' || $ribbon === 'best'): ?>
                    <span class="status-pill" style="background: rgba(197, 163, 88, 0.15); color: var(--admin-gold); border: 1px solid rgba(197, 163, 88, 0.3);">
                      ★ Bestseller
                    </span>
                  <?php elseif ($ribbon === 'new_arrival' || $ribbon === 'new'): ?>
                    <span class="status-pill" style="background: rgba(142, 25, 45, 0.2); color: #ff7675; border: 1px solid rgba(142, 25, 45, 0.4);">
                      ✨ New
                    </span>
                  <?php elseif ($ribbon === 'hot'): ?>
                    <span class="status-pill" style="background: rgba(230, 126, 34, 0.2); color: #e67e22; border: 1px solid rgba(230, 126, 34, 0.4);">
                      🔥 Hot
                    </span>
                  <?php else: ?>
                    <span style="color:var(--admin-muted); font-size:0.75rem;">—</span>
                  <?php endif; ?>
                </div>
              </td>
              <td>
                <?php if ($pubStatus === 'published' || $pubStatus === 'active'): ?>
                  <span class="status-pill status-published">● Published</span>
                <?php elseif ($pubStatus === 'draft'): ?>
                  <span class="status-pill status-draft">○ Draft</span>
                <?php else: ?>
                  <span class="status-pill status-archived">Archived</span>
                <?php endif; ?>
              </td>
              <td style="text-align: right;">
                <div style="display: inline-flex; gap: 8px;">
                  <a href="../product/product.php?id=<?php echo urlencode($sku); ?>&category=<?php echo urlencode($p['category']); ?>" target="_blank" rel="noopener" class="btn-admin btn-admin-ghost" style="padding: 5px 10px; font-size: 0.68rem;" title="Preview live PDP">
                    View ↗
                  </a>
                  <a href="product-edit.php?id=<?php echo urlencode($p['id']); ?>" class="btn-admin btn-admin-gold" style="padding: 5px 10px; font-size: 0.68rem;">
                    Edit
                  </a>
                  <form method="POST" action="product-delete.php" class="confirm-delete-form" data-name="<?php echo htmlspecialchars($p['name']); ?>" style="display:inline;">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken); ?>">
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($p['id']); ?>">
                    <button type="submit" class="btn-admin btn-admin-danger" style="padding: 5px 10px; font-size: 0.68rem;">
                      Delete
                    </button>
                  </form>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
