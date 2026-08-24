<?php
/**
 * PENNEN Admin — Edit Existing Product
 */
$pageTitle = 'Edit Product — PENNEN Product CMS';
$activeTab = 'products';

require_once __DIR__ . '/includes/header.php';

$db = PennenDB::getInstance();
$id = trim($_GET['id'] ?? '');
$product = $db->getProductById($id);

if (!$product) {
    $_SESSION['flash_error'] = "Product with identifier '{$id}' was not found.";
    header('Location: products.php');
    exit;
}

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrf = $_POST['csrf_token'] ?? '';
    if (!verifyCsrfToken($csrf)) {
        $error = 'Invalid security token. Please resubmit the form.';
    } else {
        $name = trim($_POST['name'] ?? '');
        $sku = trim($_POST['sku'] ?? ($product['sku'] ?? ''));
        if (empty($sku)) {
            $sku = 'PNN-' . strtoupper(substr(uniqid(), -6));
        } else {
            $sku = preg_replace('/[^a-zA-Z0-9_\-]/', '', $sku);
        }

        $category = $_POST['category'] ?? 'men-shoes';
        $gender = $_POST['gender'] ?? 'unisex';
        $silhouette = $_POST['silhouette'] ?? 'sneaker';
        $description = trim($_POST['description'] ?? '');
        $colorways = trim($_POST['colorways'] ?? '');

        $price = floatval($_POST['price'] ?? 0);
        $mrp = !empty($_POST['mrp']) ? floatval($_POST['mrp']) : null;
        $discount = !empty($_POST['discount']) ? floatval($_POST['discount']) : null;

        $stickerRibbon = $_POST['sticker_ribbon'] ?? 'none';
        $publicationStatus = $_POST['publication_status'] ?? 'draft';

        // Marketplace URLs
        $amazonUrl = trim($_POST['amazon_url'] ?? '');
        $flipkartUrl = trim($_POST['flipkart_url'] ?? '');
        $meeshoUrl = trim($_POST['meesho_url'] ?? '');
        $ajioUrl = trim($_POST['ajio_url'] ?? '');
        $myntraUrl = trim($_POST['myntra_url'] ?? '');
        $snapdealUrl = trim($_POST['snapdeal_url'] ?? '');
        $jiomartUrl = trim($_POST['jiomart_url'] ?? '');

        if (empty($name)) {
            $error = 'Product Name is required.';
        } elseif ($price <= 0) {
            $error = 'Please enter a valid price greater than ₹0.';
        } else {
            // Process Image Uploads (preserve existing if not replaced)
            $primaryImage = $product['image'] ?? 'hero-shoe.png';
            $hoverImage = $product['hover_image'] ?? null;
            $galleryImage1 = $product['gallery_image_1'] ?? null;
            $galleryImage2 = $product['gallery_image_2'] ?? null;

            if (!empty($_FILES['image_main']['tmp_name'])) {
                $up = handleImageUpload($_FILES['image_main'], $sku, 'main');
                if ($up) $primaryImage = $up;
            }

            if (!empty($_FILES['image_hover']['tmp_name'])) {
                $up = handleImageUpload($_FILES['image_hover'], $sku, 'hover');
                if ($up) $hoverImage = $up;
            }

            if (!empty($_FILES['image_gallery_1']['tmp_name'])) {
                $up = handleImageUpload($_FILES['image_gallery_1'], $sku, 'gallery-1');
                if ($up) $galleryImage1 = $up;
            }

            if (!empty($_FILES['image_gallery_2']['tmp_name'])) {
                $up = handleImageUpload($_FILES['image_gallery_2'], $sku, 'gallery-2');
                if ($up) $galleryImage2 = $up;
            }

            // Update Canonical Product Record
            $productRecord = [
                'id' => $product['id'],
                'sku' => $sku,
                'name' => $name,
                'category' => $category,
                'gender' => $gender,
                'silhouette' => $silhouette,
                'description' => $description,
                'colorways' => $colorways,

                'price' => $price,
                'mrp' => $mrp,
                'discount' => $discount,

                'sticker_ribbon' => $stickerRibbon,
                'publication_status' => $publicationStatus,

                'amazon_url' => !empty($amazonUrl) ? $amazonUrl : null,
                'flipkart_url' => !empty($flipkartUrl) ? $flipkartUrl : null,
                'meesho_url' => !empty($meeshoUrl) ? $meeshoUrl : null,
                'ajio_url' => !empty($ajioUrl) ? $ajioUrl : null,
                'myntra_url' => !empty($myntraUrl) ? $myntraUrl : null,
                'snapdeal_url' => !empty($snapdealUrl) ? $snapdealUrl : null,
                'jiomart_url' => !empty($jiomartUrl) ? $jiomartUrl : null,

                'image' => $primaryImage,
                'hover_image' => $hoverImage,
                'gallery_image_1' => $galleryImage1,
                'gallery_image_2' => $galleryImage2
            ];

            if ($db->saveProduct($productRecord)) {
                $_SESSION['flash_success'] = "Product '{$name}' (SKU: {$sku}) updated successfully!";
                header('Location: products.php');
                exit;
            } else {
                $error = 'Failed to update product. Please verify inputs and try again.';
            }
        }
    }
}

// Normalize current values for display
$currentSilhouette = PennenDB::normalizeSilhouette($product['silhouette'] ?? $product['shape'] ?? null);
$currentRibbon = PennenDB::normalizeStickerRibbon($product['sticker_ribbon'] ?? $product['sticker'] ?? null);
$currentGender = PennenDB::normalizeGender($product['gender'] ?? null, $product['category'] ?? '');
$currentPubStatus = $product['publication_status'] ?? $product['status'] ?? 'published';
if (!in_array($currentPubStatus, ['published', 'draft', 'archived'])) {
    $currentPubStatus = ($currentPubStatus === 'active') ? 'published' : 'draft';
}

$csrfToken = generateCsrfToken();
?>

<div class="admin-page-header">
  <div>
    <div class="admin-badge" style="margin-bottom: 8px;">Product Editor</div>
    <h1 class="admin-page-title">Edit: <?php echo htmlspecialchars($product['name']); ?></h1>
    <p class="admin-page-sub">SKU: <?php echo htmlspecialchars($product['sku'] ?? $product['id']); ?> · Modify specifications, photography, and availability.</p>
  </div>

  <div style="display:flex; gap:10px;">
    <a href="../product/product.php?id=<?php echo urlencode($product['sku'] ?? $product['id']); ?>&category=<?php echo urlencode($product['category']); ?>" target="_blank" rel="noopener" class="btn-admin btn-admin-ghost">
      View Live PDP ↗
    </a>
    <a href="products.php" class="btn-admin btn-admin-ghost">← All Products</a>
  </div>
</div>

<?php if ($error): ?>
  <div class="alert alert-error">
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
    <span><?php echo htmlspecialchars($error); ?></span>
  </div>
<?php endif; ?>

<form method="POST" action="product-edit.php?id=<?php echo urlencode($id); ?>" enctype="multipart/form-data">
  <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken); ?>">

  <div class="form-grid">
    <!-- LEFT COLUMN: Product Information -->
    <div>
      <!-- 1. Essential Details -->
      <div class="form-card">
        <div class="form-card-title">1. Essential Product Information</div>

        <div class="form-group">
          <label class="form-label" for="prodName">Product Name *</label>
          <input type="text" id="prodName" name="name" class="form-input" required value="<?php echo htmlspecialchars($product['name'] ?? ''); ?>">
        </div>

        <div class="form-row-2">
          <div class="form-group">
            <label class="form-label" for="prodSku">SKU / Product ID</label>
            <input type="text" id="prodSku" name="sku" class="form-input" value="<?php echo htmlspecialchars($product['sku'] ?? $product['id'] ?? ''); ?>">
          </div>

          <div class="form-group">
            <label class="form-label" for="prodCat">Category *</label>
            <select id="prodCat" name="category" class="form-select" required>
              <option value="men-shoes" <?php echo ($product['category'] ?? '') === 'men-shoes' ? 'selected' : ''; ?>>Men's Shoes</option>
              <option value="men-slippers" <?php echo ($product['category'] ?? '') === 'men-slippers' ? 'selected' : ''; ?>>Men's Slippers</option>
              <option value="women-shoes" <?php echo ($product['category'] ?? '') === 'women-shoes' ? 'selected' : ''; ?>>Women's Shoes</option>
              <option value="women-slippers" <?php echo ($product['category'] ?? '') === 'women-slippers' ? 'selected' : ''; ?>>Women's Slippers</option>
            </select>
          </div>
        </div>

        <div class="form-row-2">
          <div class="form-group">
            <label class="form-label" for="prodGender">Gender Target</label>
            <select id="prodGender" name="gender" class="form-select">
              <option value="unisex" <?php echo $currentGender === 'unisex' ? 'selected' : ''; ?>>Unisex</option>
              <option value="male" <?php echo $currentGender === 'male' ? 'selected' : ''; ?>>Male</option>
              <option value="female" <?php echo $currentGender === 'female' ? 'selected' : ''; ?>>Female</option>
            </select>
          </div>

          <div class="form-group">
            <label class="form-label" for="prodSilhouette">Silhouette</label>
            <select id="prodSilhouette" name="silhouette" class="form-select">
              <option value="sneaker" <?php echo $currentSilhouette === 'sneaker' ? 'selected' : ''; ?>>Sneaker</option>
              <option value="high-top" <?php echo $currentSilhouette === 'high-top' ? 'selected' : ''; ?>>High-Top</option>
              <option value="slip-on" <?php echo $currentSilhouette === 'slip-on' ? 'selected' : ''; ?>>Slip-On</option>
              <option value="slide" <?php echo $currentSilhouette === 'slide' ? 'selected' : ''; ?>>Slide</option>
              <option value="mule-clog" <?php echo $currentSilhouette === 'mule-clog' ? 'selected' : ''; ?>>Mule / Clog</option>
              <option value="flip-flop" <?php echo $currentSilhouette === 'flip-flop' ? 'selected' : ''; ?>>Flip-Flop</option>
              <option value="ballet-flat" <?php echo $currentSilhouette === 'ballet-flat' ? 'selected' : ''; ?>>Ballet Flat</option>
              <option value="wedge" <?php echo $currentSilhouette === 'wedge' ? 'selected' : ''; ?>>Wedge</option>
              <option value="loafer" <?php echo $currentSilhouette === 'loafer' ? 'selected' : ''; ?>>Loafer</option>
            </select>
          </div>
        </div>

        <div class="form-group">
          <label class="form-label" for="prodDesc">Product Description</label>
          <textarea id="prodDesc" name="description" class="form-textarea"><?php echo htmlspecialchars($product['description'] ?? ''); ?></textarea>
        </div>

        <div class="form-group">
          <label class="form-label" for="prodColorways">Colorways (Comma-separated Hex / Names)</label>
          <input type="text" id="prodColorways" name="colorways" class="form-input" value="<?php echo htmlspecialchars($product['colorways'] ?? ''); ?>">
        </div>
      </div>

      <!-- 2. Marketplace Destinations -->
      <div class="form-card">
        <div class="form-card-title">2. Marketplace Destinations ("WHERE TO BUY")</div>

        <div class="form-group">
          <label class="form-label" for="mAmazon">Amazon India URL</label>
          <input type="url" id="mAmazon" name="amazon_url" class="form-input" placeholder="https://www.amazon.in/dp/..." value="<?php echo htmlspecialchars($product['amazon_url'] ?? ''); ?>">
        </div>

        <div class="form-group">
          <label class="form-label" for="mFlipkart">Flipkart URL</label>
          <input type="url" id="mFlipkart" name="flipkart_url" class="form-input" placeholder="https://www.flipkart.com/..." value="<?php echo htmlspecialchars($product['flipkart_url'] ?? ''); ?>">
        </div>

        <div class="form-row-2">
          <div class="form-group">
            <label class="form-label" for="mMeesho">Meesho URL</label>
            <input type="url" id="mMeesho" name="meesho_url" class="form-input" placeholder="https://www.meesho.com/..." value="<?php echo htmlspecialchars($product['meesho_url'] ?? ''); ?>">
          </div>
          <div class="form-group">
            <label class="form-label" for="mAjio">AJIO URL</label>
            <input type="url" id="mAjio" name="ajio_url" class="form-input" placeholder="https://www.ajio.com/..." value="<?php echo htmlspecialchars($product['ajio_url'] ?? ''); ?>">
          </div>
        </div>

        <div class="form-row-2">
          <div class="form-group">
            <label class="form-label" for="mMyntra">Myntra URL</label>
            <input type="url" id="mMyntra" name="myntra_url" class="form-input" placeholder="https://www.myntra.com/..." value="<?php echo htmlspecialchars($product['myntra_url'] ?? ''); ?>">
          </div>
          <div class="form-group">
            <label class="form-label" for="mSnapdeal">Snapdeal URL</label>
            <input type="url" id="mSnapdeal" name="snapdeal_url" class="form-input" placeholder="https://www.snapdeal.com/..." value="<?php echo htmlspecialchars($product['snapdeal_url'] ?? ''); ?>">
          </div>
        </div>

        <div class="form-group">
          <label class="form-label" for="mJiomart">JioMart URL</label>
          <input type="url" id="mJiomart" name="jiomart_url" class="form-input" placeholder="https://www.jiomart.com/..." value="<?php echo htmlspecialchars($product['jiomart_url'] ?? ''); ?>">
        </div>
      </div>
    </div>

    <!-- RIGHT COLUMN: Pricing, Badges, Images, Status -->
    <div>
      <!-- 3. Pricing & Badges -->
      <div class="form-card">
        <div class="form-card-title">3. Pricing &amp; Visibility Badges</div>

        <div class="form-row-3">
          <div class="form-group">
            <label class="form-label" for="productPrice">Price (₹) *</label>
            <input type="number" step="0.01" id="productPrice" name="price" class="form-input" required value="<?php echo htmlspecialchars((string)(float)($product['price'] ?? 0)); ?>">
          </div>

          <div class="form-group">
            <label class="form-label" for="productMrp">MRP (₹)</label>
            <input type="number" step="0.01" id="productMrp" name="mrp" class="form-input" value="<?php echo !empty($product['mrp']) ? htmlspecialchars((string)(float)$product['mrp']) : ''; ?>">
          </div>

          <div class="form-group">
            <label class="form-label" for="productDiscount">Discount (%)</label>
            <input type="number" step="0.01" id="productDiscount" name="discount" class="form-input" value="<?php echo !empty($product['discount']) ? htmlspecialchars((string)(float)$product['discount']) : ''; ?>">
          </div>
        </div>

        <div class="form-group">
          <label class="form-label" for="prodStickerRibbon">Sticker Ribbon</label>
          <select id="prodStickerRibbon" name="sticker_ribbon" class="form-select">
            <option value="none" <?php echo $currentRibbon === 'none' ? 'selected' : ''; ?>>None</option>
            <option value="new_arrival" <?php echo $currentRibbon === 'new_arrival' ? 'selected' : ''; ?>>✨ New Arrival</option>
            <option value="bestseller" <?php echo $currentRibbon === 'bestseller' ? 'selected' : ''; ?>>★ Bestseller</option>
            <option value="hot" <?php echo $currentRibbon === 'hot' ? 'selected' : ''; ?>>🔥 Hot</option>
          </select>
        </div>

        <div class="form-group" style="margin-top: 20px;">
          <label class="form-label" for="prodPublicationStatus">Publication Status</label>
          <select id="prodPublicationStatus" name="publication_status" class="form-select">
            <option value="published" <?php echo $currentPubStatus === 'published' ? 'selected' : ''; ?>>● Published (Immediately Live on Website)</option>
            <option value="draft" <?php echo $currentPubStatus === 'draft' ? 'selected' : ''; ?>>○ Draft (Hidden from Public)</option>
            <option value="archived" <?php echo $currentPubStatus === 'archived' ? 'selected' : ''; ?>>Archived</option>
          </select>
        </div>
      </div>

      <!-- 4. Image Uploads -->
      <div class="form-card">
        <div class="form-card-title">4. Footwear Imagery Upload</div>

        <!-- Current / New Main Photo -->
        <div class="form-group">
          <label class="form-label">Primary Photo</label>
          <div class="upload-box">
            <input type="file" name="image_main" class="upload-input" data-preview="previewMain" accept="image/jpeg,image/png,image/webp">
            <div class="upload-txt">Click to replace Primary Image</div>
            <?php 
              $mainSrc = !empty($product['image']) ? '../' . $product['image'] : '../hero-shoe.png';
            ?>
            <img id="previewMain" src="<?php echo htmlspecialchars($mainSrc); ?>" class="upload-preview active" alt="Main Photo">
          </div>
        </div>

        <!-- Current / New Hover Photo -->
        <div class="form-group">
          <label class="form-label">Hover Perspective Photo</label>
          <div class="upload-box">
            <input type="file" name="image_hover" class="upload-input" data-preview="previewHover" accept="image/jpeg,image/png,image/webp">
            <div class="upload-txt">Click to replace Hover Image</div>
            <?php if (!empty($product['hover_image'])): ?>
              <img id="previewHover" src="<?php echo htmlspecialchars('../' . $product['hover_image']); ?>" class="upload-preview active" alt="Hover Photo">
            <?php else: ?>
              <img id="previewHover" class="upload-preview" alt="Hover Photo">
            <?php endif; ?>
          </div>
        </div>

        <!-- Current / New Gallery Images -->
        <div class="form-row-2">
          <div class="form-group">
            <label class="form-label">Gallery Angle 1</label>
            <div class="upload-box" style="padding:12px;">
              <input type="file" name="image_gallery_1" class="upload-input" data-preview="previewG1" accept="image/jpeg,image/png,image/webp">
              <div class="upload-txt">Gallery 1</div>
              <?php if (!empty($product['gallery_image_1'])): ?>
                <img id="previewG1" src="<?php echo htmlspecialchars('../' . $product['gallery_image_1']); ?>" class="upload-preview active" alt="Gallery 1">
              <?php else: ?>
                <img id="previewG1" class="upload-preview" alt="Gallery 1">
              <?php endif; ?>
            </div>
          </div>

          <div class="form-group">
            <label class="form-label">Gallery Angle 2</label>
            <div class="upload-box" style="padding:12px;">
              <input type="file" name="image_gallery_2" class="upload-input" data-preview="previewG2" accept="image/jpeg,image/png,image/webp">
              <div class="upload-txt">Gallery 2</div>
              <?php if (!empty($product['gallery_image_2'])): ?>
                <img id="previewG2" src="<?php echo htmlspecialchars('../' . $product['gallery_image_2']); ?>" class="upload-preview active" alt="Gallery 2">
              <?php else: ?>
                <img id="previewG2" class="upload-preview" alt="Gallery 2">
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>

      <!-- Submit Actions -->
      <div style="display: flex; gap: 12px; margin-top: 10px;">
        <button type="submit" class="btn-admin btn-admin-gold" style="flex-grow: 1; padding: 14px;">
          Update &amp; Sync Product →
        </button>
      </div>
    </div>
  </div>
</form>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
