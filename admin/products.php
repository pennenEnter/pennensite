<?php
/**
 * PENNEN Admin — Product Management Catalogue
 */
$pageTitle = 'All Products — PENNEN Product CMS';
$activeTab = 'products';

require_once __DIR__ . '/includes/header.php';

$db = PennenDB::getInstance();

$filterCat = $_GET['category'] ?? 'all';
$filterStatus = $_GET['status'] ?? 'all';
$searchQuery = trim($_GET['q'] ?? '');

$products = $db->getAllProducts([
    'category' => $filterCat,
    'publication_status' => $filterStatus,
    'search' => $searchQuery
]);

$csrfToken = generateCsrfToken();
?>

<div class="admin-page-header">
  <div>
    <div class="admin-badge" style="margin-bottom: 8px;">Catalogue Inventory</div>
    <h1 class="admin-page-title">Manage Products</h1>
    <p class="admin-page-sub">View, filter, edit, and organize all footwear silhouettes across the PENNEN showcase.</p>
  </div>

  <div>
    <a href="product-add.php" class="btn-admin btn-admin-gold">+ Add New Product</a>
  </div>
</div>

<!-- Search & Filter Controls Bar -->
<div class="table-card" style="padding: 20px 24px; margin-bottom: 24px;">
  <form method="GET" action="products.php" style="display: flex; gap: 16px; flex-wrap: wrap; align-items: flex-end;">
    <div style="flex-grow: 1; min-width: 240px;">
      <label class="form-label" for="searchInp">Search Silhouettes</label>
      <input type="text" id="searchInp" name="q" class="form-input" placeholder="Search by name, SKU, or keywords..." value="<?php echo htmlspecialchars($searchQuery); ?>">
    </div>

    <div style="min-width: 180px;">
      <label class="form-label" for="catInp">Category</label>
      <select id="catInp" name="category" class="form-select">
        <option value="all" <?php echo $filterCat === 'all' ? 'selected' : ''; ?>>All Categories</option>
        <option value="men-shoes" <?php echo $filterCat === 'men-shoes' ? 'selected' : ''; ?>>Men's Shoes</option>
        <option value="men-slippers" <?php echo $filterCat === 'men-slippers' ? 'selected' : ''; ?>>Men's Slippers</option>
        <option value="women-shoes" <?php echo $filterCat === 'women-shoes' ? 'selected' : ''; ?>>Women's Shoes</option>
        <option value="women-slippers" <?php echo $filterCat === 'women-slippers' ? 'selected' : ''; ?>>Women's Slippers</option>
      </select>
    </div>

    <div style="min-width: 150px;">
      <label class="form-label" for="statusInp">Status</label>
      <select id="statusInp" name="status" class="form-select">
        <option value="all" <?php echo $filterStatus === 'all' ? 'selected' : ''; ?>>All Statuses</option>
        <option value="published" <?php echo $filterStatus === 'published' ? 'selected' : ''; ?>>Published</option>
        <option value="draft" <?php echo $filterStatus === 'draft' ? 'selected' : ''; ?>>Draft</option>
        <option value="archived" <?php echo $filterStatus === 'archived' ? 'selected' : ''; ?>>Archived</option>
      </select>
    </div>

    <div style="display: flex; gap: 10px;">
      <button type="submit" class="btn-admin btn-admin-primary">Filter</button>
      <?php if ($filterCat !== 'all' || $filterStatus !== 'all' || !empty($searchQuery)): ?>
        <a href="products.php" class="btn-admin btn-admin-ghost">Reset</a>
      <?php endif; ?>
    </div>
  </form>
</div>

<!-- Products Table -->
<div class="table-card">
  <div class="table-header">
    <span class="table-title">
      Showing <?php echo count($products); ?> Product<?php echo count($products) === 1 ? '' : 's'; ?>
    </span>
  </div>

  <?php if (empty($products)): ?>
    <div style="padding: 60px 20px; text-align: center; color: var(--admin-muted); font-family: var(--font-mono);">
      No products match the selected criteria. <a href="product-add.php" style="color:var(--admin-gold);">+ Add a product</a> or reset filters.
    </div>
  <?php else: ?>
    <div style="overflow-x: auto;">
      <table class="admin-table">
        <thead>
          <tr>
            <th style="width: 70px;">Image</th>
            <th>Product Name &amp; SKU</th>
            <th>Category / Silhouette</th>
            <th>Pricing</th>
            <th>Badges</th>
            <th>Status</th>
            <th style="text-align: right;">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($products as $p): ?>
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
                <?php if (!empty($p['discount'])): ?>
                  <span style="font-family: var(--font-mono); font-size: 0.65rem; color: #2ecc71; font-weight: 700;">
                    <?php echo htmlspecialchars($p['discount']); ?>% OFF
                  </span>
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
                      ✨ New Arrival
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
