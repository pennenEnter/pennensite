<?php
/**
 * PENNEN Footwear — Unified Luxury Product Detail Showcase (PDP)
 */
$rootPath = '../';

$id = isset($_GET['id']) ? trim($_GET['id']) : '';
$category = isset($_GET['category']) ? trim($_GET['category']) : 'men-shoes';

$allowedCategories = [
    'men-shoes' => ['file' => 'men-shoes.json', 'title' => "Men's Shoes", 'url' => 'catalogue/men-shoes.php'],
    'men-slippers' => ['file' => 'men-slippers.json', 'title' => "Men's Slippers", 'url' => 'catalogue/men-slippers.php'],
    'women-slippers' => ['file' => 'women-slippers.json', 'title' => "Women's Slippers", 'url' => 'catalogue/women-slippers.php'],
    'women-shoes' => ['file' => 'women-shoes.json', 'title' => "Women's Shoes", 'url' => 'catalogue/women-shoes.php']
];

$catConfig = $allowedCategories[$category] ?? $allowedCategories['men-shoes'];
$jsonPath = __DIR__ . '/../data/' . $catConfig['file'];

$product = null;
$relatedProducts = [];

if (file_exists($jsonPath)) {
    $raw = json_decode(file_get_contents($jsonPath), true);
    $items = isset($raw['products']) ? $raw['products'] : $raw;
    if (is_array($items)) {
        foreach ($items as $item) {
            if ((string)($item['id'] ?? '') === (string)$id || (string)($item['sku'] ?? '') === (string)$id || (string)($item['db_id'] ?? '') === (string)$id) {
                $product = $item;
                break;
            }
        }
        if (!$product && !empty($items)) {
            $product = $items[0]; // Fallback to first item
        }

        // Collect 4 related products (excluding active product)
        foreach ($items as $item) {
            if ((string)($item['id'] ?? '') !== (string)($product['id'] ?? '')) {
                $relatedProducts[] = $item;
            }
            if (count($relatedProducts) >= 4) break;
        }
    }
}

// Fallback product data if not found
if (!$product) {
    $product = [
        'id' => '606',
        'name' => 'PENNEN Apex Pace',
        'shape' => 'sneaker',
        'image' => 'hero-shoe.png',
        'hoverImage' => 'cat-men-shoes.png',
        'price' => 2069,
        'mrp' => 4599,
        'discount' => 55,
        'description' => 'High-performance technical footwear engineered for Indian roads, resilient dual-density cushioning, and long distance durability.',
        'colors' => ['#141312', '#9E1B32', '#EDE7D8'],
        'sticker' => 'best',
        'q' => 'PENNEN+Apex+Pace'
    ];
}

$pageTitle = htmlspecialchars($product['name']) . " — PENNEN Luxury Footwear";
$metaDescription = htmlspecialchars($product['description']) . " Engineered by PENNEN in India. Available on Amazon, Flipkart, Meesho, AJIO, Myntra, Snapdeal, and JioMart.";
$activeNav = $category;
$extraCss = ['assets/css/catalogue.css'];

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/announcement-bar.php';
include __DIR__ . '/../includes/navbar.php';
include __DIR__ . '/../includes/menu-drawer.php';

// Platforms configuration
$platformsFile = __DIR__ . '/../data/platforms.json';
$rawPlatforms = file_exists($platformsFile) ? json_decode(file_get_contents($platformsFile), true) : [];
$query = $product['q'] ?? urlencode($product['name']);

// Check if product has explicit marketplace URLs entered via Admin
$hasExplicitUrls = !empty($product['amazon_url']) || !empty($product['flipkart_url']) || 
                    !empty($product['meesho_url']) || !empty($product['snapdeal_url']) || 
                    !empty($product['jiomart_url']) || !empty($product['ajio_url']) || 
                    !empty($product['myntra_url']);

$validMarketplaces = [];
foreach ($rawPlatforms as $key => $pl) {
    $name = $pl['name'] ?? ucfirst($key);
    $logo = $pl['logo'] ?? '';
    $explicitUrl = $product[$key . '_url'] ?? null;

    if ($hasExplicitUrls) {
        // Only render marketplaces where explicit URL is populated
        if (!empty($explicitUrl)) {
            $validMarketplaces[] = [
                'key' => $key,
                'name' => $name,
                'url' => $explicitUrl,
                'logo' => $logo
            ];
        }
    } else {
        // Fallback to platform search template
        $searchTpl = $pl['searchUrl'] ?? '';
        if (!empty($searchTpl) && !empty($name)) {
            $validMarketplaces[] = [
                'key' => $key,
                'name' => $name,
                'url' => str_replace('{q}', $query, $searchTpl),
                'logo' => $logo
            ];
        }
    }
}

// Build gallery image list
$galleryImages = [];
if (!empty($product['image'])) {
    $galleryImages[] = ['src' => $rootPath . $product['image'], 'label' => 'Primary Angle'];
}
if (!empty($product['hoverImage']) && $product['hoverImage'] !== $product['image']) {
    $galleryImages[] = ['src' => $rootPath . $product['hoverImage'], 'label' => 'Alternate Perspective'];
}
if (!empty($product['gallery_images']) && is_array($product['gallery_images'])) {
    foreach ($product['gallery_images'] as $idx => $gPath) {
        if (!empty($gPath)) {
            $galleryImages[] = ['src' => $rootPath . $gPath, 'label' => 'Gallery View ' . ($idx + 1)];
        }
    }
}
if (count($galleryImages) < 2 && file_exists(__DIR__ . '/../hero-shoe.png') && ($product['image'] !== 'hero-shoe.png')) {
    $galleryImages[] = ['src' => $rootPath . 'hero-shoe.png', 'label' => 'Technical Profile'];
}
?>

<main class="p-detail">
  <!-- Breadcrumb Navigation -->
  <nav class="crumb">
    <a href="<?php echo $rootPath; ?>index.php">Home</a> /
    <a href="<?php echo $rootPath . $catConfig['url']; ?>"><?php echo htmlspecialchars($catConfig['title']); ?></a> /
    <span><?php echo htmlspecialchars($product['name']); ?></span>
  </nav>

  <!-- 2-Column Product Showcase Grid -->
  <div class="p-grid">
    <!-- LEFT: Large Product Image Gallery -->
    <div class="p-gallery">
      <div class="p-main-img-wrap bracket">
        <?php if (!empty($product['sticker'])): ?>
          <span class="card-sticker card-sticker--<?php echo htmlspecialchars($product['sticker']); ?>">
            <?php 
              $s = $product['sticker'];
              echo $s === 'best' ? '★ Bestseller' : ($s === 'new' ? '✨ New Arrival' : '🔥 Featured'); 
            ?>
          </span>
        <?php endif; ?>
        <img id="mainDisplayImg" src="<?php echo $galleryImages[0]['src'] ?? ($rootPath . 'hero-shoe.png'); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" />
      </div>

      <!-- Large Interactive Thumbnail Strip -->
      <?php if (count($galleryImages) > 1): ?>
        <div class="p-thumbs">
          <?php foreach ($galleryImages as $idx => $gImg): ?>
            <div class="p-thumb <?php echo $idx === 0 ? 'active' : ''; ?>" onclick="switchPhoto('<?php echo htmlspecialchars($gImg['src']); ?>', this)" title="<?php echo htmlspecialchars($gImg['label']); ?>">
              <img src="<?php echo htmlspecialchars($gImg['src']); ?>" alt="<?php echo htmlspecialchars($product['name'] . ' - ' . $gImg['label']); ?>" />
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>

    <!-- RIGHT: Product Information & Marketplace Selector -->
    <div class="p-info">
      <div class="eyebrow"><?php echo htmlspecialchars($catConfig['title']); ?> · 100% Indian Engineering</div>
      <h1 class="display p-title"><?php echo htmlspecialchars($product['name']); ?></h1>
      <p class="p-desc"><?php echo htmlspecialchars($product['description']); ?></p>

      <!-- Price Breakdown -->
      <div class="p-price-box">
        <span class="p-price-cur">₹<?php echo number_format($product['price']); ?></span>
        <?php if (!empty($product['mrp'])): ?>
          <span class="p-price-mrp">₹<?php echo number_format($product['mrp']); ?></span>
        <?php endif; ?>
        <?php if (!empty($product['discount'])): ?>
          <span class="p-badge-disc"><?php echo htmlspecialchars($product['discount']); ?>% SAVINGS</span>
        <?php endif; ?>
      </div>

      <!-- Live Stock & Marketplace Status Indicator -->
      <div class="p-avail-tag">In Stock across Marketplace Partners</div>

      <!-- Colorways -->
      <?php if (!empty($product['colors'])): ?>
        <div style="margin-bottom: 24px;">
          <div class="p-specs-title" style="margin-bottom:10px;">Available Colorways</div>
          <div class="swatches">
            <?php foreach ($product['colors'] as $col): ?>
              <i style="background:<?php echo htmlspecialchars($col); ?>; width: 24px; height: 24px; box-shadow: 0 2px 6px rgba(0,0,0,0.15);"></i>
            <?php endforeach; ?>
          </div>
        </div>
      <?php endif; ?>

      <!-- Technical Craftsmanship Matrix -->
      <div class="p-specs-card">
        <div class="p-specs-title">Technical Craftsmanship Standards</div>
        <div class="p-specs-grid">
          <div class="p-spec-item">
            <span class="p-spec-k">Cushioning Matrix</span>
            <span class="p-spec-v">Dual-Density High-Rebound Layer</span>
          </div>
          <div class="p-spec-item">
            <span class="p-spec-k">Traction &amp; Grip</span>
            <span class="p-spec-v">Reinforced All-Season Compound</span>
          </div>
          <div class="p-spec-item">
            <span class="p-spec-k">Fit &amp; Sizing</span>
            <span class="p-spec-v">Engineered for Indian Foot Anatomy</span>
          </div>
          <div class="p-spec-item">
            <span class="p-spec-k">Craftsmanship</span>
            <span class="p-spec-v">Handcrafted &amp; Tested in India 🇮🇳</span>
          </div>
        </div>
      </div>

      <!-- ── "WHERE TO BUY" Marketplace Selection Component ── -->
      <div class="where-to-buy-card bracket-in">
        <div class="where-to-buy-header">
          <h2 class="where-to-buy-title">Where to Buy</h2>
          <p class="where-to-buy-sub">Shop this product on your preferred shopping destination with verified inventory and secure marketplace checkout:</p>
        </div>

        <div class="where-to-buy-list">
          <?php foreach ($validMarketplaces as $market): ?>
            <a href="<?php echo htmlspecialchars($market['url']); ?>" target="_blank" rel="noopener noreferrer" class="where-to-buy-btn" title="Shop <?php echo htmlspecialchars($product['name']); ?> on <?php echo htmlspecialchars($market['name']); ?>">
              <div class="where-to-buy-left">
                <?php if (!empty($market['logo'])): ?>
                  <img src="<?php echo $rootPath . $market['logo']; ?>" alt="<?php echo htmlspecialchars($market['name']); ?>" class="where-to-buy-logo" />
                <?php endif; ?>
                <span class="where-to-buy-name"><?php echo htmlspecialchars($market['name']); ?></span>
              </div>
              <span class="where-to-buy-arrow">→</span>
            </a>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- Return to Catalogue Action Buttons -->
      <div style="margin-top: 32px; display: flex; gap: 16px; flex-wrap: wrap;">
        <a href="<?php echo $rootPath . $catConfig['url']; ?>" class="btn btn-ghost">
          ← Back to <?php echo htmlspecialchars($catConfig['title']); ?>
        </a>
        <a href="<?php echo $rootPath; ?>index.php" class="btn btn-ghost">
          All Collections
        </a>
      </div>
    </div>
  </div>

  <!-- ── "You May Also Like" Related Products Section ── -->
  <?php if (!empty($relatedProducts)): ?>
    <section class="related-section">
      <div class="related-head">
        <div>
          <div class="eyebrow" style="margin-bottom:8px;">Curated Recommendations</div>
          <h2 class="display">You May Also <span class="accent accent-gold">Like.</span></h2>
        </div>
        <a href="<?php echo $rootPath . $catConfig['url']; ?>" class="sec-link-arrow">View Entire Collection →</a>
      </div>

      <div class="related-grid">
        <?php foreach ($relatedProducts as $rel): ?>
          <?php 
            $relUrl = $rootPath . 'product/product.php?id=' . urlencode($rel['id']) . '&category=' . urlencode($category);
            $relImg = $rootPath . ($rel['image'] ?? 'hero-shoe.png');
            $relHover = !empty($rel['hoverImage']) ? $rootPath . $rel['hoverImage'] : $relImg;
          ?>
          <article class="product-card bracket-in in">
            <div class="card-img-wrap">
              <?php if (!empty($rel['sticker'])): ?>
                <span class="card-sticker card-sticker--<?php echo htmlspecialchars($rel['sticker']); ?>">
                  <?php 
                    $rs = $rel['sticker'];
                    echo $rs === 'best' ? '★ Bestseller' : ($rs === 'new' ? '✨ New' : '🔥 Featured'); 
                  ?>
                </span>
              <?php endif; ?>
              <span class="price-tag">₹<?php echo number_format($rel['price']); ?></span>
              <a href="<?php echo $relUrl; ?>" class="card-img-link" title="Explore <?php echo htmlspecialchars($rel['name']); ?>">
                <img src="<?php echo $relImg; ?>" alt="<?php echo htmlspecialchars($rel['name']); ?>" class="product-img main-img" loading="lazy" />
                <img src="<?php echo $relHover; ?>" alt="<?php echo htmlspecialchars($rel['name']); ?> alternate" class="product-img hover-img" loading="lazy" />
              </a>
            </div>
            <div class="card-body">
              <p class="card-cat"><?php echo htmlspecialchars($catConfig['title']); ?></p>
              <h3 class="card-title"><a href="<?php echo $relUrl; ?>"><?php echo htmlspecialchars($rel['name']); ?></a></h3>
              <div class="card-price-row" style="margin-bottom:12px;">
                <?php if (!empty($rel['mrp'])): ?>
                  <span class="price-old">₹<?php echo number_format($rel['mrp']); ?></span>
                <?php endif; ?>
                <?php if (!empty($rel['discount'])): ?>
                  <span class="price-discount"><?php echo htmlspecialchars($rel['discount']); ?>% OFF</span>
                <?php endif; ?>
              </div>
              <a href="<?php echo $relUrl; ?>" class="card-action-link" style="padding:10px 14px;font-size:0.7rem;">
                <span>Discover Silhouette</span>
                <span class="card-arrow">→</span>
              </a>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    </section>
  <?php endif; ?>
</main>

<script>
  function switchPhoto(src, thumbEl) {
    var mainImg = document.getElementById('mainDisplayImg');
    if (mainImg) {
      mainImg.style.opacity = '0.3';
      setTimeout(function() {
        mainImg.src = src;
        mainImg.style.opacity = '1';
      }, 150);
    }
    document.querySelectorAll('.p-thumb').forEach(function(t) { t.classList.remove('active'); });
    if (thumbEl) thumbEl.classList.add('active');
  }
</script>

<?php
include __DIR__ . '/../includes/footer.php';
?>
