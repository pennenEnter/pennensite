/**
 * PENNEN Footwear — Unified Luxury Discovery Catalogue Renderer
 */
(function (window) {
  'use strict';

  function peso(n) {
    return '₹' + Number(n).toLocaleString('en-IN');
  }

  function stickerHtml(s) {
    if (!s) return '';
    const labelMap = {
      best: '★ Bestseller',
      new: '✨ New Arrival',
      hot: '🔥 Featured'
    };
    const label = labelMap[s];
    return label ? '<span class="card-sticker card-sticker--' + s + '">' + label + '</span>' : '';
  }

  function PennenCatalogue(config) {
    this.container = document.getElementById(config.containerId || 'grid');
    this.countEl = document.getElementById(config.countId || 'count');
    this.sortEl = document.getElementById(config.sortId || 'sort');
    this.priceEl = document.getElementById(config.priceRangeId || 'priceFilter');
    this.badgeEl = document.getElementById(config.badgeFilterId || 'badgeFilter');
    this.chips = document.querySelectorAll(config.chipsSelector || '.chip');
    this.dataUrl = config.dataUrl;
    this.assetPrefix = config.assetPrefix || '';
    this.categoryName = config.categoryName || 'Footwear';
    this.categorySlug = config.categorySlug || (config.dataUrl ? config.dataUrl.replace(/.*\/([^/]+)\.json$/, '$1') : 'men-shoes');

    this.products = [];
    this.view = [];
    this.activeFilter = 'all';
    this.activePriceRange = 'all';
    this.activeBadgeFilter = 'all';

    this.init();
  }

  PennenCatalogue.prototype.init = function () {
    const self = this;

    // Hook up sorting
    if (this.sortEl) {
      this.sortEl.addEventListener('change', function () {
        self.applyAllFilters();
      });
    }

    // Hook up price range filter
    if (this.priceEl) {
      this.priceEl.addEventListener('change', function () {
        self.activePriceRange = self.priceEl.value;
        self.applyAllFilters();
      });
    }

    // Hook up badge/collection filter
    if (this.badgeEl) {
      this.badgeEl.addEventListener('change', function () {
        self.activeBadgeFilter = self.badgeEl.value;
        self.applyAllFilters();
      });
    }

    // Hook up silhouette chips
    if (this.chips && this.chips.length) {
      this.chips.forEach(function (ch) {
        ch.addEventListener('click', function () {
          self.chips.forEach(function (c) { c.classList.remove('active'); });
          ch.classList.add('active');
          self.activeFilter = ch.dataset.f || 'all';
          self.applyAllFilters();
        });
      });
    }

    // Load data from JSON
    if (this.dataUrl) {
      fetch(this.dataUrl)
        .then(function (res) { return res.json(); })
        .then(function (data) {
          if (Array.isArray(data)) {
            self.products = data;
          } else if (data.products && Array.isArray(data.products)) {
            self.products = data.products;
          }
          self.view = self.products.slice();
          self.applyAllFilters();
        })
        .catch(function (err) {
          console.error('Failed to load catalogue data:', err);
          if (self.container) {
            self.container.innerHTML = '<p class="empty">Unable to load catalogue items at this time.</p>';
          }
        });
    }
  };

  PennenCatalogue.prototype.cardHtml = function (p) {
    const dots = (p.colors || []).map(function (s) {
      return '<i style="background:' + s + '"></i>';
    }).join('');

    const catSlug = p.category || this.categorySlug || 'men-shoes';
    const detailUrl = this.assetPrefix + 'product/product.php?id=' + encodeURIComponent(p.id) + '&category=' + encodeURIComponent(catSlug);

    const mainSrc = this.assetPrefix + p.image;
    const hoverSrc = p.hoverImage ? this.assetPrefix + p.hoverImage : mainSrc;

    const mrpPrice = p.mrp ? '<span class="price-old">' + peso(p.mrp) + '</span>' : '';
    const discBadge = p.discount ? '<span class="price-discount">' + p.discount + '% SAVINGS</span>' : '';

    return '<article class="product-card bracket-in" data-shape="' + (p.shape || 'all') + '">' +
      '<div class="card-img-wrap">' +
        stickerHtml(p.sticker) +
        '<span class="price-tag">' + peso(p.price) + '</span>' +
        '<a href="' + detailUrl + '" class="card-img-link" title="Explore ' + p.name + '">' +
          '<img src="' + mainSrc + '" alt="' + p.name + '" class="product-img main-img" loading="lazy">' +
          '<img src="' + hoverSrc + '" alt="' + p.name + ' alternate perspective" class="product-img hover-img" loading="lazy">' +
        '</a>' +
      '</div>' +
      '<div class="card-body">' +
        '<p class="card-cat">' + this.categoryName + '</p>' +
        '<h3 class="card-title"><a href="' + detailUrl + '">' + p.name + '</a></h3>' +
        '<p class="card-desc">' + p.description + '</p>' +
        '<div class="swatches">' + dots + '</div>' +
        '<div class="card-price-row">' + mrpPrice + discBadge + '</div>' +
        '<a href="' + detailUrl + '" class="card-action-link" title="View details and specs">' +
          '<span>Discover Silhouette</span>' +
          '<span class="card-arrow">→</span>' +
        '</a>' +
      '</div></article>';
  };

  PennenCatalogue.prototype.applyAllFilters = function () {
    const self = this;
    let list = this.products.slice();

    // 1. Silhouette filter
    if (this.activeFilter !== 'all') {
      list = list.filter(function (p) {
        return (p.shape || '').toLowerCase() === self.activeFilter.toLowerCase();
      });
    }

    // 2. Price range filter
    if (this.activePriceRange === 'under1500') {
      list = list.filter(function (p) { return p.price < 1500; });
    } else if (this.activePriceRange === '1500to2500') {
      list = list.filter(function (p) { return p.price >= 1500 && p.price <= 2500; });
    } else if (this.activePriceRange === 'above2500') {
      list = list.filter(function (p) { return p.price > 2500; });
    }

    // 3. Badge/Collection filter
    if (this.activeBadgeFilter === 'new') {
      list = list.filter(function (p) { return p.sticker === 'new'; });
    } else if (this.activeBadgeFilter === 'featured') {
      list = list.filter(function (p) { return p.sticker === 'best' || p.sticker === 'hot'; });
    }

    // 4. Sorting
    const sortVal = this.sortEl ? this.sortEl.value : 'featured';
    if (sortVal === 'low') {
      list.sort(function (a, b) { return a.price - b.price; });
    } else if (sortVal === 'high') {
      list.sort(function (a, b) { return b.price - a.price; });
    } else if (sortVal === 'disc') {
      list.sort(function (a, b) { return (b.discount || 0) - (a.discount || 0); });
    } else if (sortVal === 'newest') {
      list.sort(function (a, b) {
        if (a.sticker === 'new' && b.sticker !== 'new') return -1;
        if (b.sticker === 'new' && a.sticker !== 'new') return 1;
        return Number(b.id || 0) - Number(a.id || 0);
      });
    }

    this.view = list;
    this.render();
  };

  PennenCatalogue.prototype.render = function () {
    if (!this.container) return;

    if (!this.view.length) {
      this.container.innerHTML = '<p class="empty">No footwear matches your selected filter criteria. Try adjusting your filters.</p>';
    } else {
      const self = this;
      this.container.innerHTML = this.view.map(function (p) { return self.cardHtml(p); }).join('');

      const cards = [].slice.call(this.container.querySelectorAll('.product-card'));
      cards.forEach(function (el, i) {
        const d = Math.min(i, 16) * 35;
        setTimeout(function () {
          el.classList.add('in');
        }, 20 + d);
      });
    }

    if (this.countEl) {
      this.countEl.innerHTML = this.view.length + ' styles <span>in this collection</span>';
    }
  };

  window.PennenCatalogue = PennenCatalogue;
})(window);
