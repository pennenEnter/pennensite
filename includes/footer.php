<?php
/**
 * PENNEN Footwear — Reusable Footer & Shared SVG Component
 *
 * @var string $rootPath
 */
$rootPath = $rootPath ?? './';
?>
<!-- Crest Symbol Definition for SVG Use -->
<svg width="0" height="0" style="position:absolute" aria-hidden="true">
  <defs>
    <linearGradient id="goldFace" x1="0" y1="0" x2="1" y2="1">
      <stop offset="0" stop-color="#C23150"/>
      <stop offset=".55" stop-color="#9E1B32"/>
      <stop offset="1" stop-color="#5C1020"/>
    </linearGradient>
    <path id="crestArcTop" d="M28,100 a72,72 0 0,1 144,0" />
    <path id="crestArcBot" d="M172,100 a72,72 0 0,1 -144,0" />
    <symbol id="crest" viewBox="0 0 200 200">
      <circle cx="100" cy="100" r="92" fill="none" stroke="#9E1B32" stroke-width="1.2"/>
      <circle cx="100" cy="100" r="80" fill="none" stroke="#9E1B32" stroke-width="1" stroke-dasharray="1.6 5.2"/>
      <text font-family="JetBrains Mono, monospace" font-size="11" font-weight="700" letter-spacing="4" fill="#ffffff">
        <textPath href="#crestArcTop" startOffset="50%" text-anchor="middle">PENNEN</textPath>
      </text>
      <text font-family="JetBrains Mono, monospace" font-size="8" font-weight="600" letter-spacing="3" fill="rgba(255,255,255,.72)">
        <textPath href="#crestArcBot" startOffset="50%" text-anchor="middle">A GROWING STEP</textPath>
      </text>
      <path d="M100 58 L142 142 L58 142 Z" fill="url(#goldFace)"/>
      <path d="M100 58 L114 118 L78 110 Z" fill="#fff" opacity=".92"/>
      <path d="M100 58 L88 102 L78 110 Z" fill="#fff" opacity=".3"/>
      <line x1="76" y1="150" x2="124" y2="150" stroke="#9E1B32" stroke-width="3"/>
    </symbol>
  </defs>
</svg>

<!-- Main Footer -->
<footer class="footer" id="footer">
  <svg class="footer-crest" viewBox="0 0 200 200" aria-hidden="true">
    <use href="#crest" />
  </svg>
  <div class="footer-inner">
    <div class="footer-brand">
      <a href="<?php echo $rootPath; ?>index.php" class="brand" aria-label="PENNEN — A Growing Step">
        <img class="crest-use footer-logo" src="<?php echo $rootPath; ?>pennen-icon.png" alt="PENNEN" style="height:64px;width:auto;" />
      </a>
      <p style="margin-top:16px;font-size:0.88rem;line-height:1.7;color:rgba(239,233,222,0.7);max-width:340px;">
        An engineered Indian footwear label — luxury ergonomics, honest cushioning, and accessible pricing across every major marketplace.
      </p>
    </div>
    <div class="footer-col">
      <h4>Explore</h4>
      <a href="<?php echo $rootPath; ?>index.php">Home Store</a>
      <a href="<?php echo $rootPath; ?>catalogue/men-shoes.php">All Collections</a>
      <a href="<?php echo $rootPath; ?>search.php">Search Footwear</a>
      <a href="<?php echo $rootPath; ?>index.php#story">Our Story</a>
      <a href="#footer">Contact</a>
    </div>
    <div class="footer-col">
      <h4>Collections</h4>
      <a href="<?php echo $rootPath; ?>catalogue/men-shoes.php">Men's Shoes</a>
      <a href="<?php echo $rootPath; ?>catalogue/men-slippers.php">Men's Slippers</a>
      <a href="<?php echo $rootPath; ?>catalogue/women-shoes.php">Women's Shoes</a>
      <a href="<?php echo $rootPath; ?>catalogue/women-slippers.php">Women's Slippers</a>
    </div>
    <div class="footer-col">
      <h4>Shop On</h4>
      <a href="https://www.amazon.in" target="_blank" rel="noopener">Amazon</a>
      <a href="https://www.flipkart.com" target="_blank" rel="noopener">Flipkart</a>
      <a href="https://www.meesho.com" target="_blank" rel="noopener">Meesho</a>
      <a href="https://www.myntra.com" target="_blank" rel="noopener">Myntra</a>
      <a href="https://www.ajio.com" target="_blank" rel="noopener">AJIO</a>
    </div>
    <div class="footer-col">
      <h4>Reach Us</h4>
      <a href="https://wa.me/919289084530" target="_blank" rel="noopener">WhatsApp</a>
      <a href="mailto:support@pennen.in">Email Support</a>
      <a href="#footer">Returns Policy</a>
      <a href="#footer">Help Desk</a>
    </div>
  </div>
  <div class="footer-bot">
    <span>© <?php echo date('Y'); ?> PENNEN — A GROWING STEP. ALL RIGHTS RESERVED.</span>
    <span>MADE IN INDIA 🇮🇳</span>
  </div>
</footer>

<!-- Interactive Quick Search Modal Overlay (Global) -->
<div class="search-modal" id="searchModal" aria-hidden="true" onclick="handleSearchModalClick(event)">
  <div class="search-modal-box">
    <div class="search-modal-head">
      <div class="eyebrow">Search Collections</div>
      <button class="menu-close" onclick="closeSearchModal()" aria-label="Close search">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M6 6l12 12M18 6L6 18" />
        </svg>
      </button>
    </div>
    <form action="<?php echo $rootPath; ?>search.php" method="GET" class="search-modal-form" role="search">
      <div class="search-input-wrap">
        <svg class="search-input-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line>
        </svg>
        <input type="text" name="q" class="search-input" id="quickSearchInput" placeholder="Search by product name (e.g. Apex, Velocity)..." autocomplete="off" autofocus />
      </div>
    </form>
    <div class="search-suggestions">
      <div class="search-sugg-title">Popular Searches</div>
      <div class="search-pills">
        <a href="<?php echo $rootPath; ?>search.php?q=Apex" class="search-pill">Apex Pace</a>
        <a href="<?php echo $rootPath; ?>search.php?q=Velocity" class="search-pill">Velocity Street High</a>
        <a href="<?php echo $rootPath; ?>search.php?q=Sprint" class="search-pill">Pulse Sprint</a>
        <a href="<?php echo $rootPath; ?>search.php?q=Slide" class="search-pill">Cushioned Slides</a>
        <a href="<?php echo $rootPath; ?>search.php?q=Mule" class="search-pill">Mules &amp; Clogs</a>
      </div>
    </div>
  </div>
</div>

<!-- Floating WhatsApp Action Button -->
<a href="https://wa.me/919289084530" class="wa" target="_blank" rel="noopener" aria-label="Chat on WhatsApp">
  <svg width="18" height="18" viewBox="0 0 32 32" fill="currentColor">
    <path d="M16 3C9 3 3.5 8.5 3.5 15.5c0 2.3.6 4.4 1.7 6.3L3 29l7.4-2c1.8 1 3.9 1.5 6.1 1.5 6.9 0 12.5-5.6 12.5-12.5S22.9 3 16 3zm0 22.6c-1.9 0-3.7-.5-5.3-1.5l-.4-.2-4 1.1 1.1-3.9-.2-.4c-1.1-1.7-1.6-3.6-1.6-5.6C5.6 9.7 10.3 5 16 5s10.4 4.7 10.4 10.5S21.7 25.6 16 25.6z" />
  </svg>
  WhatsApp
</a>

<!-- Global Scripts -->
<script src="<?php echo $rootPath; ?>assets/js/main.js"></script>
</body>
</html>
