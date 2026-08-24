<?php
/**
 * PENNEN Footwear — Reusable Mobile Menu Drawer Component
 *
 * @var string $rootPath
 */
$rootPath = $rootPath ?? './';
?>
<!-- Mobile Slide-in Drawer Menu -->
<div class="menu-overlay" id="menuOverlay"></div>
<aside class="menu-drawer" id="menuDrawer" aria-label="Main menu" aria-hidden="true">
  <div class="menu-top">
    <span class="brand" aria-hidden="true" style="display:flex;align-items:center;gap:10px;">
      <img class="crest-use" src="<?php echo $rootPath; ?>pennen-icon.png" alt="PENNEN" style="height:36px;width:auto;" />
      <span class="logo-word" style="font-family:var(--font-display);font-size:22px;letter-spacing:0.04em;color:var(--color-text);">PENNEN</span>
    </span>
    <button class="menu-close" id="menuClose" aria-label="Close menu">
      <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round">
        <path d="M6 6l12 12M18 6L6 18" />
      </svg>
    </button>
  </div>

  <!-- Mobile Drawer Search Bar -->
  <form class="menu-search-form" action="<?php echo $rootPath; ?>search.php" method="GET" role="search" style="margin-top: 20px; margin-bottom: 8px; position: relative;">
    <svg style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--color-faint); pointer-events: none;" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
      <circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line>
    </svg>
    <input type="text" name="q" class="search-input" placeholder="Search product name..." value="<?php echo htmlspecialchars($_GET['q'] ?? ''); ?>" style="padding-left: 42px; font-size: 0.88rem; height: 44px; width: 100%; border-radius: var(--r-pill);" autocomplete="off" />
  </form>

  <nav class="menu-primary">
    <a href="<?php echo $rootPath; ?>index.php#arrivals">New &amp; Featured <span class="chev">→</span></a>
    <a href="<?php echo $rootPath; ?>catalogue/men-shoes.php">Men's Shoes <span class="chev">→</span></a>
    <a href="<?php echo $rootPath; ?>catalogue/women-shoes.php">Women's Shoes <span class="chev">→</span></a>
    <a href="<?php echo $rootPath; ?>catalogue/men-slippers.php">Men's Slippers <span class="chev">→</span></a>
    <a href="<?php echo $rootPath; ?>catalogue/women-slippers.php">Women's Slippers <span class="chev">→</span></a>
    <a href="<?php echo $rootPath; ?>search.php">Search All Footwear <span class="chev">→</span></a>
  </nav>
  <div class="menu-secondary">
    <a href="<?php echo $rootPath; ?>index.php#story">Our Story</a>
    <a href="#footer">Find a Store</a>
    <a href="#footer">Contact Us</a>
    <a href="#footer">Help &amp; FAQs</a>
    <a href="#footer">Shipping</a>
    <a href="#footer">Returns</a>
  </div>
  <div class="menu-foot">
    <a href="https://wa.me/919289084530" target="_blank" rel="noopener" class="menu-chat">
      <svg width="16" height="16" viewBox="0 0 32 32" fill="currentColor">
        <path d="M16 3C9 3 3.5 8.5 3.5 15.5c0 2.3.6 4.4 1.7 6.3L3 29l7.4-2c1.8 1 3.9 1.5 6.1 1.5 6.9 0 12.5-5.6 12.5-12.5S22.9 3 16 3z" />
      </svg>
      Chat with us
    </a>
    <div class="menu-social">
      <a href="#" aria-label="Instagram">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <rect x="2" y="2" width="20" height="20" rx="5" />
          <circle cx="12" cy="12" r="4" />
          <circle cx="17.5" cy="6.5" r="1" fill="currentColor" stroke="none" />
        </svg>
      </a>
      <a href="#" aria-label="Facebook">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
          <path d="M14 9h3V6h-3c-2 0-3 1-3 3v2H8v3h3v7h3v-7h3l1-3h-4V9z" />
        </svg>
      </a>
      <a href="#" aria-label="YouTube">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
          <path d="M22 8s-.2-1.5-.8-2.1c-.8-.8-1.6-.8-2-.9C16.5 4.7 12 4.7 12 4.7s-4.5 0-7.2.3c-.4.1-1.2.1-2 .9C2.2 6.5 2 8 2 8s-.2 1.7-.2 3.5v1c0 1.8.2 3.5.2 3.5s.2 1.5.8 2.1c.8.8 1.8.8 2.3.9 1.7.2 7 .3 7 .3s4.5 0 7.2-.3c.4-.1 1.2-.1 2-.9.6-.6.8-2.1.8-2.1s.2-1.7.2-3.5v-1c0-1.8-.2-3.5-.2-3.5zM10 14.5v-5l4.5 2.5L10 14.5z" />
        </svg>
      </a>
    </div>
  </div>
</aside>
