<?php
/**
 * PENNEN Footwear — Dedicated Product Search Results Showcase
 */
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';

$rootPath = './';
$query = trim($_GET['q'] ?? '');

$db = PennenDB::getInstance();
$results = [];

if ($query !== '') {
    $results = $db->searchProductsByName($query);
}

$pageTitle = $query !== '' ? "Search: " . htmlspecialchars($query) . " — PENNEN Footwear" : "Search Silhouettes — PENNEN Footwear";
$metaDescription = "Search high-performance footwear and slippers across the PENNEN catalogue. Engineered for Indian roads with honest cushioning.";
$activeNav = 'search';
$extraCss = ['assets/css/catalogue.css'];

include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/announcement-bar.php';
include __DIR__ . '/includes/navbar.php';
include __DIR__ . '/includes/menu-drawer.php';

// Category titles mapping
$catTitles = [
    'men-shoes' => "Men's Shoes",
    'men-slippers' => "Men's Slippers",
    'women-shoes' => "Women's Shoes",
    'women-slippers' => "Women's Slippers"
];
?>

<main>
  <!-- Search Hero Header -->
  <section class="cat-hero" style="padding-bottom: 32px;">
    <nav class="crumb">
      <a href="<?php echo $rootPath; ?>index.php">Home</a> /
      <span>Search</span>
      <?php if ($query !== ''): ?>
        / <span>"<?php echo htmlspecialchars($query); ?>"</span>
      <?php endif; ?>
    </nav>

    <div class="eyebrow">Catalogue Discovery</div>
    <h1 class="display cat-title">
      <?php if ($query !== ''): ?>
        Search Results for <span class="accent accent-gold">"<?php echo htmlspecialchars($query); ?>"</span>
      <?php else: ?>
        Search <span class="accent accent-gold">Footwear Silhouettes.</span>
      <?php endif; ?>
    </h1>

    <p class="cat-sub">
      <?php if ($query !== ''): ?>
        Found <strong><?php echo count($results); ?></strong> matching style<?php echo count($results) === 1 ? '' : 's'; ?> in our collection.
      <?php else: ?>
        Enter a product name or model keyword to explore our engineered footwear collection.
      <?php endif; ?>
    </p>

    <!-- Search Form Input in Page Hero -->
    <div style="max-width: 640px; margin: 24px auto 0;">
      <form action="search.php" method="GET" style="display:flex; gap:10px; width:100%;" role="search">
        <div style="position:relative; flex-grow:1;">
          <svg style="position:absolute; left:16px; top:50%; transform:translateY(-50%); color:var(--color-faint);" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line>
          </svg>
          <input 
            type="text" 
            name="q" 
            value="<?php echo htmlspecialchars($query); ?>" 
            placeholder="Search by product name (e.g. Apex, Velocity, Sprint, Slide)..." 
            class="form-input" 
            style="padding-left:46px; border-radius:var(--r-pill); background:var(--color-surface); height:52px; font-size:1rem;" 
            autofocus 
            required 
          />
        </div>
        <button type="submit" class="btn btn-gold bracket" style="padding:0 28px; height:52px;">
          Search
        </button>
      </form>

      <!-- Popular Search Suggestions -->
      <div style="display:flex; gap:8px; align-items:center; justify-content:center; flex-wrap:wrap; margin-top:16px;">
        <span style="font-family:var(--font-mono); font-size:0.68rem; font-weight:700; color:var(--color-muted); text-transform:uppercase; letter-spacing:0.1em;">Popular:</span>
        <a href="search.php?q=Apex" class="search-pill">Apex</a>
        <a href="search.php?q=Velocity" class="search-pill">Velocity</a>
        <a href="search.php?q=Sprint" class="search-pill">Sprint</a>
        <a href="search.php?q=Street" class="search-pill">Street</a>
        <a href="search.php?q=Slide" class="search-pill">Slide</a>
        <a href="search.php?q=Cloud" class="search-pill">Cloud</a>
      </div>
    </div>
  </section>

  <div class="meander-wrap"><div class="meander" aria-hidden="true"></div></div>

  <!-- Search Results Section -->
  <section class="section-products" style="padding-top: 40px; padding-bottom: 80px;">
    <?php if ($query !== '' && empty($results)): ?>
      <!-- No Results State -->
      <div style="max-width:680px; margin:40px auto; text-align:center; padding:60px 30px; background:var(--color-surface); border:1px solid var(--color-border); border-radius:var(--r-l); box-shadow:var(--shadow-s);">
        <div style="font-size:2.5rem; margin-bottom:16px;">🔍</div>
        <h2 class="display" style="font-size:1.8rem; margin-bottom:12px;">No Matching Footwear Found</h2>
        <p style="color:var(--color-muted); font-size:1.02rem; line-height:1.6; margin-bottom:28px;">
          We couldn't find any product matching <strong>"<?php echo htmlspecialchars($query); ?>"</strong>. Try searching for a different model name or browse our curated collections:
        </p>
        <div style="display:flex; gap:12px; justify-content:center; flex-wrap:wrap;">
          <a href="catalogue/men-shoes.php" class="btn btn-ghost bracket">Men's Shoes</a>
          <a href="catalogue/women-shoes.php" class="btn btn-ghost bracket">Women's Shoes</a>
          <a href="catalogue/men-slippers.php" class="btn btn-ghost bracket">Men's Slippers</a>
          <a href="catalogue/women-slippers.php" class="btn btn-ghost bracket">Women's Slippers</a>
        </div>
      </div>
    <?php elseif (!empty($results)): ?>
      <!-- Matching Products Grid -->
      <div class="products-grid" style="display:grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap:24px;">
        <?php foreach ($results as $p): ?>
          <?php 
            $cat = $p['category'] ?? 'men-shoes';
            $sku = !empty($p['sku']) ? $p['sku'] : (string)$p['id'];
            $detailUrl = $rootPath . 'product/product.php?id=' . urlencode($sku) . '&category=' . urlencode($cat);
            $imgSrc = !empty($p['image']) ? $rootPath . $p['image'] : ($rootPath . 'hero-shoe.png');
            $hoverSrc = !empty($p['hover_image']) ? $rootPath . $p['hover_image'] : $imgSrc;
            $catLabel = $catTitles[$cat] ?? "PENNEN Footwear";

            // Parse colorways
            $colors = [];
            if (!empty($p['colorways'])) {
                $decoded = json_decode($p['colorways'], true);
                if (is_array($decoded)) {
                    $colors = $decoded;
                } else {
                    $rawC = explode(',', $p['colorways']);
                    foreach ($rawC as $c) {
                        $c = trim($c);
                        if (!empty($c)) $colors[] = $c;
                    }
                }
            }

            // Sticker ribbon
            $ribbon = $p['sticker_ribbon'] ?? 'none';
          ?>
          <article class="product-card bracket-in in" data-silhouette="<?php echo htmlspecialchars($p['silhouette'] ?? 'sneaker'); ?>">
            <div class="card-img-wrap">
              <?php if ($ribbon === 'bestseller'): ?>
                <span class="card-sticker card-sticker--best">★ Bestseller</span>
              <?php elseif ($ribbon === 'new_arrival'): ?>
                <span class="card-sticker card-sticker--new">✨ New Arrival</span>
              <?php elseif ($ribbon === 'hot'): ?>
                <span class="card-sticker card-sticker--hot">🔥 Hot</span>
              <?php endif; ?>

              <span class="price-tag">₹<?php echo number_format($p['price']); ?></span>

              <a href="<?php echo $detailUrl; ?>" class="card-img-link" title="Explore <?php echo htmlspecialchars($p['name']); ?>">
                <img src="<?php echo htmlspecialchars($imgSrc); ?>" alt="<?php echo htmlspecialchars($p['name']); ?>" class="product-img main-img" loading="lazy" />
                <img src="<?php echo htmlspecialchars($hoverSrc); ?>" alt="<?php echo htmlspecialchars($p['name']); ?> alternate angle" class="product-img hover-img" loading="lazy" />
              </a>
            </div>

            <div class="card-body">
              <p class="card-cat"><?php echo htmlspecialchars($catLabel); ?> · <?php echo htmlspecialchars(ucfirst($p['silhouette'] ?? 'sneaker')); ?></p>
              <h3 class="card-title"><a href="<?php echo $detailUrl; ?>"><?php echo htmlspecialchars($p['name']); ?></a></h3>
              <p class="card-desc"><?php echo htmlspecialchars($p['description'] ?? ''); ?></p>

              <?php if (!empty($colors)): ?>
                <div class="swatches">
                  <?php foreach ($colors as $col): ?>
                    <i style="background:<?php echo htmlspecialchars($col); ?>"></i>
                  <?php endforeach; ?>
                </div>
              <?php endif; ?>

              <div class="card-price-row">
                <?php if (!empty($p['mrp'])): ?>
                  <span class="price-old">₹<?php echo number_format($p['mrp']); ?></span>
                <?php endif; ?>
                <?php if (!empty($p['discount'])): ?>
                  <span class="price-discount"><?php echo htmlspecialchars((string)(float)$p['discount']); ?>% SAVINGS</span>
                <?php endif; ?>
              </div>

              <a href="<?php echo $detailUrl; ?>" class="card-action-link" title="View details and specs">
                <span>Discover Silhouette</span>
                <span class="card-arrow">→</span>
              </a>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </section>
</main>

<?php
include __DIR__ . '/includes/footer.php';
?>
