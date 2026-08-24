<?php
/**
 * PENNEN Footwear — Minimal Luxury Navigation Bar
 *
 * @var string $rootPath
 * @var string $activeNav
 * @var bool   $isHome
 */
$rootPath = $rootPath ?? './';
$activeNav = $activeNav ?? 'home';
$isHome = $isHome ?? false;
?>
<!-- Navigation Header -->
<header class="nav" id="nav">
  <div class="nav-inner">
    <!-- Brand Identity -->
    <a href="<?php echo $isHome ? '#home' : $rootPath . 'index.php'; ?>" class="brand" aria-label="PENNEN — A Growing Step">
      <img class="crest-use nav-logo" src="<?php echo $rootPath; ?>pennen-icon.png" alt="PENNEN" />
    </a>

    <!-- Minimal Editorial Navigation -->
    <nav class="nav-links nav-main-links" aria-label="Primary Navigation">
      <a href="<?php echo $isHome ? '#arrivals' : $rootPath . 'index.php#arrivals'; ?>" class="nav-link <?php echo $activeNav === 'arrivals' ? 'active' : ''; ?>">NEW ARRIVALS</a>
      <a href="<?php echo $rootPath; ?>catalogue/men-shoes.php" class="nav-link <?php echo ($activeNav === 'men-shoes' || $activeNav === 'men-slippers') ? 'active' : ''; ?>">MEN</a>
      <a href="<?php echo $rootPath; ?>catalogue/women-shoes.php" class="nav-link <?php echo ($activeNav === 'women-shoes' || $activeNav === 'women-slippers') ? 'active' : ''; ?>">WOMEN</a>
      <a href="<?php echo $rootPath; ?>catalogue/men-shoes.php" class="nav-link <?php echo $activeNav === 'footwear' ? 'active' : ''; ?>">FOOTWEAR</a>
      <a href="<?php echo $isHome ? '#story' : $rootPath . 'index.php#story'; ?>" class="nav-link <?php echo $activeNav === 'about' ? 'active' : ''; ?>">ABOUT</a>
    </nav>

    <!-- Right Side Actions: SEARCH & MENU -->
    <div class="nav-right-actions">
      <!-- Compact Desktop Search Form -->
      <form class="nav-search-form" action="<?php echo $rootPath; ?>search.php" method="GET" role="search">
        <svg class="nav-search-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
          <circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line>
        </svg>
        <input type="text" name="q" class="nav-search-input" placeholder="Search products..." value="<?php echo htmlspecialchars($_GET['q'] ?? ''); ?>" autocomplete="off" aria-label="Search footwear products" />
      </form>

      <!-- Search Trigger Pill for Modal / Mobile -->
      <!-- <button class="nav-action-btn nav-search-trigger" id="searchBtn" aria-label="Search PENNEN Collections" onclick="openSearchModal()">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line>
        </svg>
        <span class="nav-btn-text">SEARCH</span>
      </button> -->

      <button class="nav-action-btn menu-trigger-btn" id="burger" aria-label="Open menu" aria-controls="menuDrawer" aria-expanded="false">
        <span class="burger-bars"><span></span><span></span><span></span></span>
        <span class="nav-btn-text">MENU</span>
      </button>
    </div>
  </div>
</header>
