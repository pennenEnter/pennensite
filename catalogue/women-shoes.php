<?php
/**
 * PENNEN Footwear — Women's Shoes Catalogue
 */
$rootPath = '../';
$pageTitle = "PENNEN — Women's Footwear Collection";
$metaDescription = "PENNEN — engineered footwear, a growing step. Explore Women's Shoes collection, view craftsmanship specifications, and discover where each style is available across top Indian marketplaces.";
$activeNav = 'women-shoes';
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
      <span>Women's Shoes</span>
    </nav>
    <div class="eyebrow">Women's Footwear</div>
    <h1 class="display cat-title">Women's footwear built for <span class="accent accent-gold">poise &amp; pace.</span></h1>
    <p class="cat-sub">From modern sneakers and ballet flats to comfort wedges and loafers — discover crafted pairs with cushioned support for every occasion.</p>
    
    <!-- Category Switcher Tabs -->
    <div class="tabs">
      <a href="<?php echo $rootPath; ?>catalogue/men-shoes.php" class="tab">Men's Shoes</a>
      <a href="<?php echo $rootPath; ?>catalogue/men-slippers.php" class="tab">Men's Slippers</a>
      <a href="<?php echo $rootPath; ?>catalogue/women-shoes.php" class="tab active">Women's Shoes</a>
      <a href="<?php echo $rootPath; ?>catalogue/women-slippers.php" class="tab">Women's Slippers</a>
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
      <button class="chip" data-f="sneaker">Sneakers</button>
      <button class="chip" data-f="flat">Flats</button>
      <button class="chip" data-f="wedge">Wedges</button>
      <button class="chip" data-f="loafer">Loafers</button>
      <button class="chip" data-f="mule">Mules</button>
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
      dataUrl: '<?php echo $rootPath; ?>data/women-shoes.json',
      containerId: 'grid',
      countId: 'count',
      sortId: 'sort',
      priceRangeId: 'priceFilter',
      badgeFilterId: 'badgeFilter',
      chipsSelector: '.chip',
      assetPrefix: '<?php echo $rootPath; ?>',
      categoryName: "Women · Footwear",
      categorySlug: 'women-shoes'
    });
  });
</script>

<?php
include __DIR__ . '/../includes/footer.php';
?>
