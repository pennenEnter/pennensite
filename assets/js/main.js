/**
 * PENNEN Footwear — Main Global UI Script (Gen-3 Design System)
 */
(function () {
  'use strict';

  // ── 1. Sticky Navigation Scroll Effect ──
  const nav = document.getElementById('nav');
  if (nav) {
    window.addEventListener('scroll', function () {
      nav.classList.toggle('scrolled', window.scrollY > 14);
    }, { passive: true });
  }

  // ── 2. Mobile Slide-in Drawer Menu ──
  const burger = document.getElementById('burger');
  const drawer = document.getElementById('menuDrawer');
  const overlay = document.getElementById('menuOverlay');
  const closeBtn = document.getElementById('menuClose');

  function openMenu() {
    document.body.classList.add('menu-open');
    document.body.style.overflow = 'hidden';
    if (burger) burger.setAttribute('aria-expanded', 'true');
    if (drawer) drawer.setAttribute('aria-hidden', 'false');
  }

  function closeMenu() {
    document.body.classList.remove('menu-open');
    document.body.style.overflow = '';
    if (burger) burger.setAttribute('aria-expanded', 'false');
    if (drawer) drawer.setAttribute('aria-hidden', 'true');
  }

  if (burger) {
    burger.addEventListener('click', function () {
      if (document.body.classList.contains('menu-open')) {
        closeMenu();
      } else {
        openMenu();
      }
    });
  }

  if (closeBtn) closeBtn.addEventListener('click', closeMenu);
  if (overlay) overlay.addEventListener('click', closeMenu);

  window.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && document.body.classList.contains('menu-open')) {
      closeMenu();
    }
  });

  if (drawer) {
    drawer.querySelectorAll('a').forEach(function (a) {
      a.addEventListener('click', closeMenu);
    });
  }

  // ── 3. Scroll Reveal Animations ──
  if ('IntersectionObserver' in window) {
    const io = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          entry.target.classList.add('in');
          io.unobserve(entry.target);
        }
      });
    }, { threshold: 0.14 });

    document.querySelectorAll('.reveal').forEach(function (el) {
      io.observe(el);
    });
  } else {
    document.querySelectorAll('.reveal').forEach(function (el) {
      el.classList.add('in');
    });
  }

  // ── 4. Hero Cinematic Video Management & Fallback ──
  const heroVideo = document.getElementById('heroFootwearVideo');
  if (heroVideo) {
    const fallbackImg = heroVideo.querySelector('.hero-video-fallback-img');

    // Ensure mobile browsers recognize muted and playsinline
    heroVideo.muted = true;
    heroVideo.defaultMuted = true;
    heroVideo.playsInline = true;
    heroVideo.setAttribute('playsinline', '');
    heroVideo.setAttribute('webkit-playsinline', '');
    heroVideo.setAttribute('muted', '');

    function showFallback() {
      heroVideo.style.display = 'none';
      if (fallbackImg) {
        fallbackImg.style.display = 'block';
      }
    }

    heroVideo.addEventListener('error', showFallback);

    // Ensure autoplay plays smoothly
    const playPromise = heroVideo.play();
    if (playPromise !== undefined) {
      playPromise.catch(function () {
        // Autoplay prevented by browser power-saving; attempt play on first user interaction
        const triggerPlay = function () {
          heroVideo.play().catch(function () {});
          window.removeEventListener('touchstart', triggerPlay);
          window.removeEventListener('scroll', triggerPlay);
          window.removeEventListener('click', triggerPlay);
        };
        window.addEventListener('touchstart', triggerPlay, { passive: true, once: true });
        window.addEventListener('scroll', triggerPlay, { passive: true, once: true });
        window.addEventListener('click', triggerPlay, { passive: true, once: true });
      });
    }

    // ── 5. Subtle Luxury Parallax on Desktop ──
    const heroStage = document.getElementById('heroVideoStage');
    const techCard = document.getElementById('heroTechCard');
    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    if (heroStage && !prefersReducedMotion && window.innerWidth > 860) {
      let mouseX = 0;
      let mouseY = 0;
      let targetX = 0;
      let targetY = 0;
      let isHovering = false;
      let ticking = false;

      const heroSection = document.querySelector('.hero');
      if (heroSection) {
        heroSection.addEventListener('mousemove', function (e) {
          const rect = heroSection.getBoundingClientRect();
          const cx = rect.left + rect.width / 2;
          const cy = rect.top + rect.height / 2;
          targetX = (e.clientX - cx) / (rect.width / 2);
          targetY = (e.clientY - cy) / (rect.height / 2);
          isHovering = true;
          if (!ticking) {
            ticking = true;
            requestAnimationFrame(updateParallax);
          }
        }, { passive: true });

        heroSection.addEventListener('mouseleave', function () {
          targetX = 0;
          targetY = 0;
          isHovering = false;
        });
      }

      function updateParallax() {
        mouseX += (targetX - mouseX) * 0.08;
        mouseY += (targetY - mouseY) * 0.08;

        if (heroStage) {
          const rotateY = mouseX * 2.5;
          const rotateX = -mouseY * 2.5;
          heroStage.style.transform = 'perspective(1000px) rotateX(' + rotateX.toFixed(2) + 'deg) rotateY(' + rotateY.toFixed(2) + 'deg)';
        }

        if (techCard) {
          const shiftX = mouseX * 8;
          const shiftY = mouseY * 6;
          techCard.style.transform = 'translate(' + shiftX.toFixed(2) + 'px, ' + shiftY.toFixed(2) + 'px)';
        }

        if (isHovering || Math.abs(targetX - mouseX) > 0.001 || Math.abs(targetY - mouseY) > 0.001) {
          requestAnimationFrame(updateParallax);
        } else {
          ticking = false;
        }
      }
    }
  }

  // ── 6. Global Quick Search Modal Controller ──
  window.openSearchModal = function () {
    const modal = document.getElementById('searchModal');
    if (modal) {
      modal.classList.add('active');
      modal.setAttribute('aria-hidden', 'false');
      setTimeout(function () {
        const inp = document.getElementById('quickSearchInput');
        if (inp) inp.focus();
      }, 100);
    }
  };

  window.closeSearchModal = function () {
    const modal = document.getElementById('searchModal');
    if (modal) {
      modal.classList.remove('active');
      modal.setAttribute('aria-hidden', 'true');
    }
  };

  window.handleSearchModalClick = function (e) {
    if (e && e.target && e.target.id === 'searchModal') {
      window.closeSearchModal();
    }
  };

  window.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
      window.closeSearchModal();
    }
  });
})();
