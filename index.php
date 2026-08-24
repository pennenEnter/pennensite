<?php
/**
 * PENNEN Footwear — Luxury Brand Showcase & Product Discovery Homepage
 */
$rootPath = './';
$pageTitle = 'PENNEN — Luxury Indian Footwear Brand & Showcase';
$metaDescription = 'Discover PENNEN — technical footwear crafted with honest Indian craftsmanship. Discover products here and buy on Amazon, Flipkart, Meesho, AJIO, Myntra, Snapdeal, and JioMart.';
$activeNav = 'home';
$isHome = true;
$extraCss = ['assets/css/home.css'];

// Load product data for Featured Products and New Arrivals
$menShoesFile = __DIR__ . '/data/men-shoes.json';
$womenShoesFile = __DIR__ . '/data/women-shoes.json';

$menShoes = file_exists($menShoesFile) ? json_decode(file_get_contents($menShoesFile), true) : [];
$womenShoes = file_exists($womenShoesFile) ? json_decode(file_get_contents($womenShoesFile), true) : [];

// Safe list extraction
$allMen = is_array($menShoes) ? (isset($menShoes['products']) ? $menShoes['products'] : $menShoes) : [];
$allWomen = is_array($womenShoes) ? (isset($womenShoes['products']) ? $womenShoes['products'] : $womenShoes) : [];

// Curate Featured Products (3 key styles)
$featuredProducts = array_slice($allMen, 0, 3);
if (empty($featuredProducts)) {
    $featuredProducts = [
        [
            'id' => '606',
            'name' => 'PENNEN Apex Pace',
            'category' => 'men-shoes',
            'categoryName' => "Men's Running",
            'price' => 2069,
            'mrp' => 4599,
            'discount' => 55,
            'image' => 'images/shoes/606/main.jpg',
            'hoverImage' => 'images/shoes/606/hover.jpg',
            'description' => 'Cushioned trainer built for all-day wear and distance walking.'
        ],
        [
            'id' => '608',
            'name' => 'PENNEN Velocity Street High',
            'category' => 'men-shoes',
            'categoryName' => "Men's High-Top",
            'price' => 1449,
            'mrp' => 2629,
            'discount' => 45,
            'image' => 'images/shoes/608/main.jpg',
            'hoverImage' => 'images/shoes/608/hover.jpg',
            'description' => 'Street-ready high-top silhouette with ankle stability.'
        ],
        [
            'id' => '609',
            'name' => 'PENNEN Urban Street High',
            'category' => 'men-shoes',
            'categoryName' => "Men's High-Top",
            'price' => 1449,
            'mrp' => 2629,
            'discount' => 45,
            'image' => 'images/shoes/609/main.jpg',
            'hoverImage' => 'images/shoes/609/hover.jpg',
            'description' => 'Clean urban high-top profile for daily comfort.'
        ]
    ];
}

// Curate New Arrivals (4 fresh silhouettes)
$newArrivals = array_slice($allMen, 3, 2);
if (!empty($allWomen)) {
    $newArrivals = array_merge($newArrivals, array_slice($allWomen, 0, 2));
}

include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/announcement-bar.php';
include __DIR__ . '/includes/navbar.php';
include __DIR__ . '/includes/menu-drawer.php';
?>

<main>
  <!-- ── 1. Hero Section (Cinematic Footwear Motion & Brand Narrative) ── -->
  <section class="hero" id="home">
    <div class="hero-grid">
      <!-- Left Editorial Copy Column -->
      <div class="hero-copy">
        <div class="hero-kicker eyebrow hero-anim-eyebrow">Technical footwear · Made in India</div>
        <h1 class="display hero-title">
          <span class="hero-line-wrap"><span class="hero-line hero-line-1">Walk bold.</span></span>
          <span class="hero-line-wrap"><span class="hero-line hero-line-2">Grow with every</span></span>
          <span class="hero-line-wrap"><span class="hero-line hero-line-3"><span class="accent accent-gold">single step.</span></span></span>
        </h1>
        <p class="hero-lead hero-anim-desc">
          PENNEN is high-performance footwear for real Indian distance and honest comfort. Discover our curated silhouettes here, then purchase seamlessly across your trusted marketplace.
        </p>
        <div class="hero-actions hero-anim-cta">
          <a href="#featured" class="btn btn-primary bracket" id="heroExploreBtn">Explore Collection <span class="arrow">→</span></a>
          <a href="#story" class="btn btn-ghost bracket" id="heroStoryBtn">Discover PENNEN</a>
        </div>
        <div class="hero-stats hero-anim-stats">
          <div class="stat"><div class="num"><em>20k+</em></div><div class="lbl">Happy Walkers</div></div>
          <div class="stat"><div class="num">7</div><div class="lbl">Marketplaces</div></div>
          <div class="stat"><div class="num">4.8<em>★</em></div><div class="lbl">Average Rating</div></div>
        </div>
      </div>

      <!-- Right Cinematic Footwear Video Stage -->
      <div class="hero-video-stage hero-anim-video" id="heroVideoStage">
        <div class="hero-video-plate bracket">
          <div class="hero-video-wrap">
            <video class="hero-cinematic-video" id="heroFootwearVideo" autoplay muted loop playsinline preload="metadata" poster="<?php echo $rootPath; ?>hero_video_poster.jpg" aria-label="PENNEN Footwear Motion Campaign Video">
              <source src="<?php echo $rootPath; ?>assets/vedio/hero_video.mp4" type="video/mp4" />
              <img src="<?php echo $rootPath; ?>hero_video_poster.jpg" alt="PENNEN Footwear Campaign" class="hero-video-fallback-img" />
            </video>
            <div class="hero-video-scrim"></div>
            <!-- Live Footwear Campaign Tag -->
            <!-- <div class="hero-video-live-pill">
              <span class="live-dot"></span>
              <span class="live-text">CAMPAIGN FILM // 01</span>
            </div> -->
          </div>

          <!-- Precision Technical Specs Badges -->
          <!-- <span class="hero-spec-tag tag-top-right">PENNEN Pace // Dual-Density</span>
          <span class="hero-spec-tag tag-bot-left">Engineered in India 🇮🇳</span> -->
        </div>

        <!-- Floating Technical Glass Card -->
        <div class="hero-tech-card" id="heroTechCard">
          <span class="k">MODEL // APEX 01</span>
          <span class="v">AIR-CUSHIONED</span>
          <!-- <span class="s">TESTED ON INDIAN ROADS</span> -->
        </div>
      </div>
    </div>

    <!-- Integrated Understated Marketplace Ribbon -->
    <div class="hero-market-strip hero-anim-market">
      <div class="hero-market-inner">
        <div class="hero-market-label">
          <span class="market-label-dot"></span>
          <span>Shop PENNEN wherever you shop</span>
        </div>
        <div class="hero-market-links">
          <a href="https://www.amazon.in/s?k=PENNEN" target="_blank" rel="noopener" class="hero-market-link" title="Shop PENNEN on Amazon">
            <img src="<?php echo $rootPath; ?>ammaloh.png" alt="Amazon" />
            <span class="market-name">Amazon</span>
          </a>
          <a href="https://www.flipkart.com/search?q=PENNEN" target="_blank" rel="noopener" class="hero-market-link" title="Shop PENNEN on Flipkart">
            <img src="<?php echo $rootPath; ?>flipkart-log-icon-e33c.png" alt="Flipkart" />
            <span class="market-name">Flipkart</span>
          </a>
          <a href="https://www.meesho.com/search?q=PENNEN" target="_blank" rel="noopener" class="hero-market-link" title="Shop PENNEN on Meesho">
            <img src="<?php echo $rootPath; ?>Meesho_logo.png" alt="Meesho" />
            <span class="market-name">Meesho</span>
          </a>
          <a href="https://www.myntra.com/PENNEN" target="_blank" rel="noopener" class="hero-market-link" title="Shop PENNEN on Myntra">
            <img src="<?php echo $rootPath; ?>Myntra.png" alt="Myntra" />
            <span class="market-name">Myntra</span>
          </a>
          <a href="https://www.ajio.com/search/?text=PENNEN" target="_blank" rel="noopener" class="hero-market-link" title="Shop PENNEN on AJIO">
            <img src="<?php echo $rootPath; ?>AJIO.png" alt="AJIO" />
            <span class="market-name">AJIO</span>
          </a>
          <a href="https://www.snapdeal.com/search?keyword=PENNEN" target="_blank" rel="noopener" class="hero-market-link" title="Shop PENNEN on Snapdeal">
            <img src="<?php echo $rootPath; ?>Snapdeal.png" alt="Snapdeal" />
            <span class="market-name">Snapdeal</span>
          </a>
          <a href="https://www.jiomart.com/search/PENNEN" target="_blank" rel="noopener" class="hero-market-link" title="Shop PENNEN on JioMart">
            <img src="<?php echo $rootPath; ?>jiomart.png" alt="JioMart" />
            <span class="market-name">JioMart</span>
          </a>
        </div>
      </div>
    </div>
  </section>

  <!-- ── 2. Marketplace Availability Statement ── -->
  <section class="statement-section">
    <div class="statement-inner">
      <div class="statement-text-col reveal">
        <div class="eyebrow" style="margin-bottom: 8px;">The PENNEN Shopping Model</div>
        <h2 class="statement-headline">
          Discover PENNEN here.<br/>
          <span class="statement-highlight">Buy PENNEN where you already shop.</span>
        </h2>
        <p class="statement-sub">
          We focus 100% on footwear testing, and luxury design. For ordering, fast delivery, and trusted payments, choose whichever marketplace you already use daily.
        </p>
      </div>

      <div class="statement-logos-col reveal d1">
        <div class="statement-logos-title">Available across leading destinations</div>
        <div class="statement-logos-grid">
          <a href="https://www.amazon.in/s?k=PENNEN" target="_blank" rel="noopener" class="statement-logo-item" title="Shop on Amazon">
            <img src="ammaloh.png" alt="Amazon" />
          </a>
          <a href="https://www.flipkart.com/search?q=PENNEN" target="_blank" rel="noopener" class="statement-logo-item" title="Shop on Flipkart">
            <img src="flipkart-log-icon-e33c.png" alt="Flipkart" />
          </a>
          <a href="https://www.meesho.com/search?q=PENNEN" target="_blank" rel="noopener" class="statement-logo-item" title="Shop on Meesho">
            <img src="Meesho_logo.png" alt="Meesho" />
          </a>
          <a href="https://www.myntra.com/PENNEN" target="_blank" rel="noopener" class="statement-logo-item" title="Shop on Myntra">
            <img src="Myntra.png" alt="Myntra" />
          </a>
          <a href="https://www.ajio.com/search/?text=PENNEN" target="_blank" rel="noopener" class="statement-logo-item" title="Shop on AJIO">
            <img src="AJIO.png" alt="AJIO" />
          </a>
          <a href="https://www.snapdeal.com/search?keyword=PENNEN" target="_blank" rel="noopener" class="statement-logo-item" title="Shop on Snapdeal">
            <img src="Snapdeal.png" alt="Snapdeal" />
          </a>
          <a href="https://www.jiomart.com/search/PENNEN" target="_blank" rel="noopener" class="statement-logo-item" title="Shop on JioMart">
            <img src="jiomart.png" alt="JioMart" />
          </a>
          <div class="statement-logo-item" style="font-family:var(--font-mono);font-size:0.68rem;font-weight:700;color:var(--color-accent);text-align:center;">
            + MORE
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ── 3. Featured Products Section ── -->
  <section class="sec-editorial" id="featured">
    <div class="sec-head-split">
      <div>
        <div class="eyebrow reveal" style="margin-bottom:10px;">Curated Highlights</div>
        <h2 class="display reveal">Featured <span class="accent accent-gold">Silhouettes.</span></h2>
      </div>
      <a href="catalogue/men-shoes.php" class="sec-link-arrow reveal d1">View All Styles →</a>
    </div>

    <div class="featured-grid">
      <?php foreach ($featuredProducts as $idx => $p): ?>
        <?php 
          $categorySlug = $p['category'] ?? 'men-shoes';
          $detailUrl = $rootPath . 'product/product.php?id=' . urlencode($p['id']) . '&category=' . urlencode($categorySlug);
          $imgSrc = $rootPath . ($p['image'] ?? 'hero-shoe.png');
          $catDisplay = $p['categoryName'] ?? (str_contains($categorySlug, 'women') ? "Women's Footwear" : "Men's Footwear");
        ?>
        <article class="feat-card bracket-in reveal <?php echo 'd' . ($idx + 1); ?>">
          <div class="feat-img-wrap">
            <a href="<?php echo $detailUrl; ?>" class="feat-img-link" title="View details of <?php echo htmlspecialchars($p['name']); ?>">
              <img src="<?php echo $imgSrc; ?>" alt="<?php echo htmlspecialchars($p['name']); ?>" class="feat-img" loading="lazy" />
            </a>
          </div>
          <div class="feat-body">
            <div class="feat-cat"><?php echo htmlspecialchars($catDisplay); ?></div>
            <h3 class="feat-title"><a href="<?php echo $detailUrl; ?>"><?php echo htmlspecialchars($p['name']); ?></a></h3>
            
            <div class="feat-price-row">
              <span class="feat-price">₹<?php echo number_format($p['price']); ?></span>
              <?php if (!empty($p['mrp'])): ?>
                <span class="feat-mrp">₹<?php echo number_format($p['mrp']); ?></span>
              <?php endif; ?>
              <?php if (!empty($p['discount'])): ?>
                <span class="feat-discount"><?php echo htmlspecialchars($p['discount']); ?>% OFF</span>
              <?php endif; ?>
            </div>

            <div class="feat-avail-label">Available on:</div>
            <div class="feat-avail-platforms">Amazon · Flipkart · Meesho · AJIO</div>

            <a href="<?php echo $detailUrl; ?>" class="feat-cta">
              <span>View Product Details</span>
              <span>→</span>
            </a>
          </div>
        </article>
      <?php endforeach; ?>
    </div>
  </section>

  <!-- ── 4. New Arrivals Section ── -->
  <section class="arrivals-section" id="arrivals">
    <div class="arrivals-inner">
      <div class="sec-head-split">
        <div>
          <div class="eyebrow reveal" style="margin-bottom:10px;">Fresh Drops</div>
          <h2 class="display reveal">New <span class="accent accent-gold">Arrivals.</span></h2>
        </div>
        <a href="catalogue/men-shoes.php" class="sec-link-arrow reveal d1">Explore All New Releases →</a>
      </div>

      <div class="arrivals-grid">
        <?php foreach ($newArrivals as $idx => $arr): ?>
          <?php 
            $catSlug = $arr['category'] ?? 'men-shoes';
            $detailUrl = $rootPath . 'product/product.php?id=' . urlencode($arr['id']) . '&category=' . urlencode($catSlug);
            $imgSrc = $rootPath . ($arr['image'] ?? 'hero-shoe.png');
            $catDisplay = str_contains($catSlug, 'women') ? "Women's Edition" : "Men's Edition";
          ?>
          <article class="arrival-card bracket-in reveal <?php echo 'd' . ($idx + 1); ?>">
            <span class="arrival-badge">✨ New Arrival</span>
            <div class="feat-img-wrap">
              <a href="<?php echo $detailUrl; ?>" class="feat-img-link">
                <img src="<?php echo $imgSrc; ?>" alt="<?php echo htmlspecialchars($arr['name']); ?>" class="feat-img" loading="lazy" />
              </a>
            </div>
            <div class="feat-body">
              <div class="feat-cat"><?php echo htmlspecialchars($catDisplay); ?></div>
              <h3 class="feat-title" style="font-size:1.15rem;"><a href="<?php echo $detailUrl; ?>"><?php echo htmlspecialchars($arr['name']); ?></a></h3>
              <div class="feat-price-row" style="margin-bottom:12px;">
                <span class="feat-price">₹<?php echo number_format($arr['price']); ?></span>
                <?php if (!empty($arr['mrp'])): ?>
                  <span class="feat-mrp">₹<?php echo number_format($arr['mrp']); ?></span>
                <?php endif; ?>
              </div>
              <a href="<?php echo $detailUrl; ?>" class="feat-cta" style="padding:10px 14px;font-size:0.7rem;">
                <span>View Product</span>
                <span>→</span>
              </a>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- ── 5. Shop by Category (Large Photography) ── -->
  <section class="sec-editorial" id="categories">
    <div class="sec-head">
      <div class="eyebrow reveal" style="margin-bottom:12px;">Architectural Range</div>
      <h2 class="display reveal">Explore by <span class="accent accent-gold">category.</span></h2>
      <p class="reveal d1" style="color:var(--color-muted);font-size:1.06rem;">Precision comfort, uniformly across every silhouette we craft.</p>
    </div>

    <div class="cat-grid-large">
      <a href="catalogue/men-shoes.php" class="cat-card-large bracket-in reveal">
        <img src="cat-men-shoes.png" alt="Men's Shoes Category" loading="lazy" />
        <div class="cat-meta">
          <div class="k">For Him · Running &amp; Street</div>
          <h3>Men's Shoes</h3>
          <span class="go">Explore Collection →</span>
        </div>
      </a>

      <a href="catalogue/men-slippers.php" class="cat-card-large bracket-in reveal d1">
        <img src="cat-men-slippers.png" alt="Men's Slippers Category" loading="lazy" />
        <div class="cat-meta">
          <div class="k">Everyday Comfort · Slides</div>
          <h3>Men's Slippers</h3>
          <span class="go">Explore Collection →</span>
        </div>
      </a>

      <a href="catalogue/women-shoes.php" class="cat-card-large bracket-in reveal d2">
        <img src="cat-women-footwear.png" alt="Women's Shoes Category" loading="lazy" />
        <div class="cat-meta">
          <div class="k">For Her · Poise &amp; Pace</div>
          <h3>Women's Shoes</h3>
          <span class="go">Explore Collection →</span>
        </div>
      </a>

      <a href="catalogue/women-slippers.php" class="cat-card-large bracket-in reveal d3">
        <img src="cat-trending.png" alt="Women's Slippers Category" loading="lazy" />
        <div class="cat-meta">
          <div class="k">Ultra-Light · Cushioned</div>
          <h3>Women's Slippers</h3>
          <span class="go">Explore Collection →</span>
        </div>
      </a>
    </div>
  </section>

  <!-- ── 6. Brand Story / Ethos ── -->
  <section class="story" id="story">
    <div class="story-inner">
      <div class="reveal">
        <div class="eyebrow on-dark">The PENNEN Philosophy</div>
        <h2 class="display" style="margin-top:18px">Built for <span class="accent accent-gold">real</span> distance.</h2>
        <p>PENNEN was born from an uncompromising standard: luxury ergonomics and technical styling should be accessible without inflated boutique markups. Every prototype is stress-tested on real Indian streets, made to carry you from morning training sessions to evening urban strolls.</p>
        <p>Honest materials, resilient dual-density cushioning, and authentic brand integrity available across whichever marketplace you prefer to shop.</p>
        <div class="story-quote">— a growing step.</div>
      </div>
      <div class="story-pic reveal d2">
        <img src="hero-shoe.png" alt="PENNEN craftsmanship" loading="lazy" />
      </div>
    </div>
  </section>

  <!-- ── 7. Product / Brand Campaign Lookbook ── -->
  <section class="campaign-section" style="margin-top:90px;">
    <div class="campaign-banner reveal">
      <div class="campaign-content">
        <div class="eyebrow on-dark">2026 Performance Series</div>
        <h2 class="campaign-headline">Made for <span class="accent" style="color:var(--color-gold-foil);">distance.</span></h2>
        <p class="campaign-copy">
          Designed with lightweight breathable mesh, high-rebound cushioning, and traction outsoles made for all weather conditions across India.
        </p>

        <div class="campaign-specs-row">
          <div class="camp-spec-item">
            <span class="camp-spec-title">Dynamic Cushion</span>
            <span class="camp-spec-desc">Absorbs shock on paved &amp; rough terrains</span>
          </div>
          <div class="camp-spec-item">
            <span class="camp-spec-title">Traction Sole</span>
            <span class="camp-spec-desc">Reinforced grip compound for Indian seasons</span>
          </div>
        </div>

        <div>
          <a href="catalogue/men-shoes.php" class="btn btn-gold bracket">Explore Performance Series →</a>
        </div>
      </div>

      <div class="campaign-visual">
        <img src="cat-men-shoes.png" alt="PENNEN Performance Campaign" loading="lazy" />
      </div>
    </div>
  </section>

  <!-- ── 8. Marketplace Discovery Section ── -->
  <section class="market-hub-section" id="marketplaces">
    <div class="market-hub-inner">
      <div class="sec-head" style="text-align:center;">
        <div class="eyebrow center reveal" style="margin-bottom:12px;">Marketplace Network</div>
        <h2 class="display reveal">Shop PENNEN on <span class="accent accent-gold">Your Platform.</span></h2>
        <p class="reveal d1" style="max-width:580px;margin:0 auto;color:var(--color-muted);">
          Click your preferred shopping app below to view live inventory, select sizing, and enjoy safe verified checkout:
        </p>
      </div>

      <div class="market-hub-grid">
        <a href="https://www.amazon.in/s?k=PENNEN" target="_blank" rel="noopener" class="market-hub-card reveal">
          <img src="ammaloh.png" alt="Amazon" />
          <h4>Amazon India</h4>
          <p>Prime Fast Delivery</p>
          <span class="market-link-btn">Shop Amazon →</span>
        </a>

        <a href="https://www.flipkart.com/search?q=PENNEN" target="_blank" rel="noopener" class="market-hub-card reveal d1">
          <img src="flipkart-log-icon-e33c.png" alt="Flipkart" />
          <h4>Flipkart</h4>
          <p>Assured Quality</p>
          <span class="market-link-btn">Shop Flipkart →</span>
        </a>

        <a href="https://www.myntra.com/PENNEN" target="_blank" rel="noopener" class="market-hub-card reveal d2">
          <img src="Myntra.png" alt="Myntra" />
          <h4>Myntra</h4>
          <p>Fashion Destination</p>
          <span class="market-link-btn">Shop Myntra →</span>
        </a>

        <a href="https://www.ajio.com/search/?text=PENNEN" target="_blank" rel="noopener" class="market-hub-card reveal d3">
          <img src="AJIO.png" alt="AJIO" />
          <h4>AJIO</h4>
          <p>Curated Trends</p>
          <span class="market-link-btn">Shop AJIO →</span>
        </a>

        <a href="https://www.meesho.com/search?q=PENNEN" target="_blank" rel="noopener" class="market-hub-card reveal d1">
          <img src="Meesho_logo.png" alt="Meesho" />
          <h4>Meesho</h4>
          <p>Value Footwear</p>
          <span class="market-link-btn">Shop Meesho →</span>
        </a>

        <a href="https://www.snapdeal.com/search?keyword=PENNEN" target="_blank" rel="noopener" class="market-hub-card reveal d2">
          <img src="Snapdeal.png" alt="Snapdeal" />
          <h4>Snapdeal</h4>
          <p>Pan-India Delivery</p>
          <span class="market-link-btn">Shop Snapdeal →</span>
        </a>

        <a href="https://www.jiomart.com/search/PENNEN" target="_blank" rel="noopener" class="market-hub-card reveal d3">
          <img src="jiomart.png" alt="JioMart" />
          <h4>JioMart</h4>
          <p>Trusted Retail</p>
          <span class="market-link-btn">Shop JioMart →</span>
        </a>
      </div>
    </div>
  </section>

  <!-- ── 9. Final Brand CTA Band ── -->
  <div class="cta-band" style="margin-top:90px;">
    <div class="cta-box reveal">
      <div class="eyebrow on-dark" style="margin-bottom:14px;">A Growing Step</div>
      <h2 class="display">Step into <span class="accent">PENNEN.</span></h2>
      <!-- <p>Discover our full spectrum of engineered footwear designed for Indian strides.</p> -->
      <div style="display:flex;gap:16px;justify-content:center;flex-wrap:wrap;">
        <a href="catalogue/men-shoes.php" class="btn btn-gold bracket">Explore Men's Collection →</a>
        <a href="catalogue/women-shoes.php" class="btn btn-ghost bracket" style="color:#FFFFFF;border-color:rgba(247,245,240,0.3);">Explore Women's Collection →</a>
      </div>
    </div>
  </div>
</main>

<?php
include __DIR__ . '/includes/footer.php';
?>
