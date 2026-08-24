<?php
/**
 * PENNEN Admin — Add New Product
 */
$pageTitle = 'Add New Product — PENNEN Product CMS';
$activeTab = 'product-add';

require_once __DIR__ . '/includes/header.php';

$error = null;
$db = PennenDB::getInstance();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrf = $_POST['csrf_token'] ?? '';
    if (!verifyCsrfToken($csrf)) {
        $error = 'Invalid security token. Please resubmit the form.';
    } else {
        $name = trim($_POST['name'] ?? '');
        $sku = trim($_POST['sku'] ?? '');
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
            // Process Image Uploads
            $primaryImage = 'hero-shoe.png';
            $hoverImage = null;
            $galleryImage1 = null;
            $galleryImage2 = null;

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

            // Canonical Product Record
            $product = [
                'name' => $name,
                'sku' => $sku,
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

            if ($db->saveProduct($product)) {
                $_SESSION['flash_success'] = "Product '{$name}' (SKU: {$sku}) saved successfully!";
                header('Location: products.php');
                exit;
            } else {
                $error = 'Failed to save product to database. Please verify all inputs and try again.';
            }
        }
    }
}

$csrfToken = generateCsrfToken();
?>

<div class="admin-page-header">
  <div>
    <div class="admin-badge" style="margin-bottom: 8px;">Product Creator</div>
    <h1 class="admin-page-title">Add New Product</h1>
    <p class="admin-page-sub">Configure silhouette details, pricing, photography, and external marketplace destinations.</p>
  </div>

  <a href="products.php" class="btn-admin btn-admin-ghost">← Back to Products</a>
</div>

<?php if ($error): ?>
  <div class="alert alert-error">
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
    <span><?php echo htmlspecialchars($error); ?></span>
  </div>
<?php endif; ?>

<form method="POST" action="product-add.php" enctype="multipart/form-data">
  <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken); ?>">

  <div class="form-grid">
    <!-- LEFT COLUMN: Product Information -->
    <div>
      <!-- 1. Essential Details -->
      <div class="form-card">
        <div class="form-card-title">1. Essential Product Information</div>

        <div class="form-group">
          <label class="form-label" for="prodName">Product Name *</label>
          <input type="text" id="prodName" name="name" class="form-input" required placeholder="e.g. PENNEN Apex Pace Pro" value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>">
        </div>

        <div class="form-row-2">
          <div class="form-group">
            <label class="form-label" for="prodSku">SKU / Product ID (Optional)</label>
            <input type="text" id="prodSku" name="sku" class="form-input" placeholder="e.g. PNN-1001 (Auto if blank)" value="<?php echo htmlspecialchars($_POST['sku'] ?? ''); ?>">
          </div>

          <div class="form-group">
            <label class="form-label" for="prodCat">Category *</label>
            <select id="prodCat" name="category" class="form-select" required>
              <option value="men-shoes" <?php echo ($_POST['category'] ?? '') === 'men-shoes' ? 'selected' : ''; ?>>Men's Shoes</option>
              <option value="men-slippers" <?php echo ($_POST['category'] ?? '') === 'men-slippers' ? 'selected' : ''; ?>>Men's Slippers</option>
              <option value="women-shoes" <?php echo ($_POST['category'] ?? '') === 'women-shoes' ? 'selected' : ''; ?>>Women's Shoes</option>
              <option value="women-slippers" <?php echo ($_POST['category'] ?? '') === 'women-slippers' ? 'selected' : ''; ?>>Women's Slippers</option>
            </select>
          </div>
        </div>

        <div class="form-row-2">
          <div class="form-group">
            <label class="form-label" for="prodGender">Gender Target</label>
            <select id="prodGender" name="gender" class="form-select">
              <option value="unisex" <?php echo ($_POST['gender'] ?? 'unisex') === 'unisex' ? 'selected' : ''; ?>>Unisex</option>
              <option value="male" <?php echo ($_POST['gender'] ?? '') === 'male' ? 'selected' : ''; ?>>Male</option>
              <option value="female" <?php echo ($_POST['gender'] ?? '') === 'female' ? 'selected' : ''; ?>>Female</option>
            </select>
          </div>

          <div class="form-group">
            <label class="form-label" for="prodSilhouette">Silhouette</label>
            <select id="prodSilhouette" name="silhouette" class="form-select">
              <option value="sneaker" <?php echo ($_POST['silhouette'] ?? 'sneaker') === 'sneaker' ? 'selected' : ''; ?>>Sneaker</option>
              <option value="high-top" <?php echo ($_POST['silhouette'] ?? '') === 'high-top' ? 'selected' : ''; ?>>High-Top</option>
              <option value="slip-on" <?php echo ($_POST['silhouette'] ?? '') === 'slip-on' ? 'selected' : ''; ?>>Slip-On</option>
              <option value="slide" <?php echo ($_POST['silhouette'] ?? '') === 'slide' ? 'selected' : ''; ?>>Slide</option>
              <option value="mule-clog" <?php echo ($_POST['silhouette'] ?? '') === 'mule-clog' ? 'selected' : ''; ?>>Mule / Clog</option>
              <option value="flip-flop" <?php echo ($_POST['silhouette'] ?? '') === 'flip-flop' ? 'selected' : ''; ?>>Flip-Flop</option>
              <option value="ballet-flat" <?php echo ($_POST['silhouette'] ?? '') === 'ballet-flat' ? 'selected' : ''; ?>>Ballet Flat</option>
              <option value="wedge" <?php echo ($_POST['silhouette'] ?? '') === 'wedge' ? 'selected' : ''; ?>>Wedge</option>
              <option value="loafer" <?php echo ($_POST['silhouette'] ?? '') === 'loafer' ? 'selected' : ''; ?>>Loafer</option>
            </select>
          </div>
        </div>

        <div class="form-group">
          <label class="form-label" for="prodDesc">Product Description</label>
          <textarea id="prodDesc" name="description" class="form-textarea" placeholder="Craftsmanship details, cushioning specs, and materials..."><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
        </div>

        <div class="form-group">
          <label class="form-label" for="prodColorways">Colorways (Comma-separated Hex / Names)</label>
          <input type="text" id="prodColorways" name="colorways" class="form-input" placeholder="#141312, #8E192D, #C5A358" value="<?php echo htmlspecialchars($_POST['colorways'] ?? '#141312, #8E192D'); ?>">
        </div>
      </div>

      <!-- 2. Marketplace Destinations -->
      <div class="form-card">
        <div class="form-card-title">2. Marketplace Destinations ("WHERE TO BUY")</div>
        <p style="color:var(--admin-muted); font-size:0.8rem; margin-bottom:18px;">
          Enter specific product listing URLs. Leave blank to omit a platform from public display.
        </p>

        <div class="form-group">
          <label class="form-label" for="mAmazon">Amazon India URL</label>
          <input type="url" id="mAmazon" name="amazon_url" class="form-input" placeholder="https://www.amazon.in/dp/..." value="<?php echo htmlspecialchars($_POST['amazon_url'] ?? ''); ?>">
        </div>

        <div class="form-group">
          <label class="form-label" for="mFlipkart">Flipkart URL</label>
          <input type="url" id="mFlipkart" name="flipkart_url" class="form-input" placeholder="https://www.flipkart.com/..." value="<?php echo htmlspecialchars($_POST['flipkart_url'] ?? ''); ?>">
        </div>

        <div class="form-row-2">
          <div class="form-group">
            <label class="form-label" for="mMeesho">Meesho URL</label>
            <input type="url" id="mMeesho" name="meesho_url" class="form-input" placeholder="https://www.meesho.com/..." value="<?php echo htmlspecialchars($_POST['meesho_url'] ?? ''); ?>">
          </div>
          <div class="form-group">
            <label class="form-label" for="mAjio">AJIO URL</label>
            <input type="url" id="mAjio" name="ajio_url" class="form-input" placeholder="https://www.ajio.com/..." value="<?php echo htmlspecialchars($_POST['ajio_url'] ?? ''); ?>">
          </div>
        </div>

        <div class="form-row-2">
          <div class="form-group">
            <label class="form-label" for="mMyntra">Myntra URL</label>
            <input type="url" id="mMyntra" name="myntra_url" class="form-input" placeholder="https://www.myntra.com/..." value="<?php echo htmlspecialchars($_POST['myntra_url'] ?? ''); ?>">
          </div>
          <div class="form-group">
            <label class="form-label" for="mSnapdeal">Snapdeal URL</label>
            <input type="url" id="mSnapdeal" name="snapdeal_url" class="form-input" placeholder="https://www.snapdeal.com/..." value="<?php echo htmlspecialchars($_POST['snapdeal_url'] ?? ''); ?>">
          </div>
        </div>

        <div class="form-group">
          <label class="form-label" for="mJiomart">JioMart URL</label>
          <input type="url" id="mJiomart" name="jiomart_url" class="form-input" placeholder="https://www.jiomart.com/..." value="<?php echo htmlspecialchars($_POST['jiomart_url'] ?? ''); ?>">
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
            <input type="number" step="0.01" id="productPrice" name="price" class="form-input" required placeholder="1499" value="<?php echo htmlspecialchars($_POST['price'] ?? ''); ?>">
          </div>

          <div class="form-group">
            <label class="form-label" for="productMrp">MRP (₹)</label>
            <input type="number" step="0.01" id="productMrp" name="mrp" class="form-input" placeholder="2999" value="<?php echo htmlspecialchars($_POST['mrp'] ?? ''); ?>">
          </div>

          <div class="form-group">
            <label class="form-label" for="productDiscount">Discount (%)</label>
            <input type="number" step="0.01" id="productDiscount" name="discount" class="form-input" placeholder="50" value="<?php echo htmlspecialchars($_POST['discount'] ?? ''); ?>">
          </div>
        </div>

        <div class="form-group">
          <label class="form-label" for="prodStickerRibbon">Sticker Ribbon</label>
          <select id="prodStickerRibbon" name="sticker_ribbon" class="form-select">
            <option value="none" <?php echo ($_POST['sticker_ribbon'] ?? 'none') === 'none' ? 'selected' : ''; ?>>None</option>
            <option value="new_arrival" <?php echo ($_POST['sticker_ribbon'] ?? '') === 'new_arrival' ? 'selected' : ''; ?>>✨ New Arrival</option>
            <option value="bestseller" <?php echo ($_POST['sticker_ribbon'] ?? '') === 'bestseller' ? 'selected' : ''; ?>>★ Bestseller</option>
            <option value="hot" <?php echo ($_POST['sticker_ribbon'] ?? '') === 'hot' ? 'selected' : ''; ?>>🔥 Hot</option>
          </select>
        </div>

        <div class="form-group" style="margin-top: 20px;">
          <label class="form-label" for="prodPublicationStatus">Publication Status</label>
          <select id="prodPublicationStatus" name="publication_status" class="form-select">
            <option value="published" <?php echo ($_POST['publication_status'] ?? '') === 'published' ? 'selected' : ''; ?>>● Published (Immediately Live on Website)</option>
            <option value="draft" <?php echo ($_POST['publication_status'] ?? 'draft') === 'draft' ? 'selected' : ''; ?>>○ Draft (Hidden from Public)</option>
            <option value="archived" <?php echo ($_POST['publication_status'] ?? '') === 'archived' ? 'selected' : ''; ?>>Archived</option>
          </select>
        </div>
      </div>

      <!-- 4. Image Uploads -->
      <div class="form-card">
        <div class="form-card-title">4. Footwear Imagery Upload</div>
        <p style="color:var(--admin-muted); font-size:0.75rem; margin-bottom:16px;">
          Supports JPG, PNG, WEBP (Max 5MB each). Images are stored safely in <code>uploads/products/{product-id}/</code>.
        </p>

        <!-- Main Photo -->
        <div class="form-group">
          <label class="form-label">Primary Footwear Photo</label>
          <div class="upload-box">
            <input type="file" name="image_main" class="upload-input" data-preview="previewMain" accept="image/jpeg,image/png,image/webp">
            <div class="upload-icon">📷</div>
            <div class="upload-txt">Click or drag &amp; drop Primary Image</div>
            <img id="previewMain" class="upload-preview" alt="Preview Main">
          </div>
        </div>

        <!-- Hover Alternate Photo -->
        <div class="form-group">
          <label class="form-label">Hover Perspective Photo</label>
          <div class="upload-box">
            <input type="file" name="image_hover" class="upload-input" data-preview="previewHover" accept="image/jpeg,image/png,image/webp">
            <div class="upload-icon">🔄</div>
            <div class="upload-txt">Click to select Alternate Hover Image</div>
            <img id="previewHover" class="upload-preview" alt="Preview Hover">
          </div>
        </div>

        <!-- Additional Gallery Images -->
        <div class="form-row-2">
          <div class="form-group">
            <label class="form-label">Gallery Angle 1</label>
            <div class="upload-box" style="padding:12px;">
              <input type="file" name="image_gallery_1" class="upload-input" data-preview="previewG1" accept="image/jpeg,image/png,image/webp">
              <div class="upload-txt">Gallery 1</div>
              <img id="previewG1" class="upload-preview" alt="Gallery 1">
            </div>
          </div>

          <div class="form-group">
            <label class="form-label">Gallery Angle 2</label>
            <div class="upload-box" style="padding:12px;">
              <input type="file" name="image_gallery_2" class="upload-input" data-preview="previewG2" accept="image/jpeg,image/png,image/webp">
              <div class="upload-txt">Gallery 2</div>
              <img id="previewG2" class="upload-preview" alt="Gallery 2">
            </div>
          </div>
        </div>
      </div>

      <!-- Submit Actions -->
      <div style="display: flex; gap: 12px; margin-top: 10px;">
        <button type="submit" class="btn-admin btn-admin-gold" style="flex-grow: 1; padding: 14px;">
          Save &amp; Publish Product →
        </button>
      </div>
    </div>
  </div>
</form>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
