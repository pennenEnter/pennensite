<?php
/**
 * PENNEN Footwear — Women's Slippers Catalogue
 */
$rootPath = '../';
$pageTitle = "PENNEN — Women's Slippers Collection";
$metaDescription = "PENNEN — engineered footwear, a growing step. Explore Women's Slippers collection, view craftsmanship specifications, and discover where each style is available across top Indian marketplaces.";
$activeNav = 'women-slippers';
$extraCss = ['assets/css/catalogue.css'];

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/announcement-bar.php';
include __DIR__ . '/../includes/navbar.php';
include __DIR__ . '/../includes/menu-drawer.php';
?>

<main>
  <!-- Category Hero -->
  <section class="cat-hero">
    <nav class="crumb">
      <a href="<?php echo $rootPath; ?>index.php">Home</a> /
      <a href="<?php echo $rootPath; ?>catalogue/men-shoes.php">Catalogue</a> /
      <span>Women's Slippers</span>
    </nav>
    <div class="eyebrow">Women's Slippers</div>
    <h1 class="display cat-title">Women's slippers for <span class="accent accent-gold">effortless grace.</span></h1>
    <p class="cat-sub">Cloud-cushioned slides, soft home mules, and light flip-flops crafted with delicate arch support for every step of your day.</p>
    
    <!-- Category Switcher Tabs -->
    <div class="tabs">
      <a href="<?php echo $rootPath; ?>catalogue/men-shoes.php" class="tab">Men's Shoes</a>
      <a href="<?php echo $rootPath; ?>catalogue/men-slippers.php" class="tab">Men's Slippers</a>
      <a href="<?php echo $rootPath; ?>catalogue/women-shoes.php" class="tab">Women's Shoes</a>
      <a href="<?php echo $rootPath; ?>catalogue/women-slippers.php" class="tab active">Women's Slippers</a>
    </div>
  </section>

  <div class="meander-wrap"><div class="meander" aria-hidden="true"></div></div>

  <!-- Multi-Dimensional Filter Controls -->
  <div class="controls">
    <div class="controls-top-row">
      <div class="count" id="count">styles <span>in this collection</span></div>
      
      <div class="filter-selects-group">
        <!-- Collection Filter -->
        <label class="filter-item-wrap">Edition:
          <select id="badgeFilter" class="filter-select">
            <option value="all">All Editions</option>
            <option value="new">✨ New Arrivals</option>
            <option value="featured">★ Bestsellers / Featured</option>
          </select>
        </label>

        <!-- Price Range Filter -->
        <label class="filter-item-wrap">Price:
          <select id="priceFilter" class="filter-select">
            <option value="all">All Price Ranges</option>
            <option value="under1500">Under ₹1,500</option>
            <option value="1500to2500">₹1,500 – ₹2,500</option>
            <option value="above2500">Above ₹2,500</option>
          </select>
        </label>

        <!-- Sort Select -->
        <label class="filter-item-wrap">Sort:
          <select id="sort" class="filter-select">
            <option value="featured">Featured Curation</option>
            <option value="newest">Newest Additions</option>
            <option value="low">Price: Low to High</option>
            <option value="high">Price: High to Low</option>
            <option value="disc">Biggest Savings</option>
          </select>
        </label>
      </div>
    </div>

    <!-- Silhouette / Product Type Chips -->
    <div class="chips">
      <button class="chip active" data-f="all">All Silhouettes</button>
      <button class="chip" data-f="slide">Slides</button>
      <button class="chip" data-f="mule">Mules</button>
      <button class="chip" data-f="flipflop">Flip-Flops</button>
    </div>
  </div>

  <!-- Products Grid -->
  <section class="section-products">
    <div class="products-grid" id="grid"></div>
  </section>
</main>

<script src="<?php echo $rootPath; ?>assets/js/catalogue.js"></script>
<script>
  document.addEventListener('DOMContentLoaded', function () {
    new PennenCatalogue({
      dataUrl: '<?php echo $rootPath; ?>data/women-slippers.json',
      containerId: 'grid',
      countId: 'count',
      sortId: 'sort',
      priceRangeId: 'priceFilter',
      badgeFilterId: 'badgeFilter',
      chipsSelector: '.chip',
      assetPrefix: '<?php echo $rootPath; ?>',
      categoryName: "Women · Slippers",
      categorySlug: 'women-slippers'
    });
  });
</script>

<?php
include __DIR__ . '/../includes/footer.php';
?>
