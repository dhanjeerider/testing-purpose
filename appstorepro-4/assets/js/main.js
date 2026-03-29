/* AppStore Pro — main.js */
(function () {
  'use strict';

  /* ── DOM Ready ── */
  document.addEventListener('DOMContentLoaded', init);

  function init() {
    if (window._pasRevealFailsafe) {
      clearTimeout(window._pasRevealFailsafe);
      window._pasRevealFailsafe = null;
    }
    initThemeRestore();
    initScrollBar();
    initDarkMode();
    initColorPicker();
    initParticles();
    initHeaderScroll();
    initSearchToggle();
    initBottomNav();
    initSlideMenu();
    initScreenshotsCarousel();
    initStickyBar();
    initTutorialAccordion();
    initYoutubeEmbed();
    initShareButton();
    initContentFold();
    initRevealAnimations();
    initSystemTheme();
  }

  /* ── Theme Restore (color + dark mode) ── */
  function initThemeRestore() {
    var saved = localStorage.getItem('pas_theme');
    if (saved === 'dark') {
      document.body.classList.add('dark-mode');
      document.documentElement.classList.add('dark-mode');
    } else if (saved === 'light') {
      document.body.classList.remove('dark-mode');
      document.documentElement.classList.remove('dark-mode');
    } else if (!saved) {
      if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
        document.body.classList.add('dark-mode');
        document.documentElement.classList.add('dark-mode');
      }
    }

    var savedColor = localStorage.getItem('pas_color');
    if (savedColor) {
      try {
        var d = JSON.parse(savedColor);
        applyColorTheme(d.primary, d.light, d.bg, d.name);
      } catch (e) { /* ignore */ }
    }
  }

  /* ── Scroll Progress Bar ── */
  function initScrollBar() {
    var bar = document.getElementById('pas-scroll-bar');
    if (!bar) return;
    window.addEventListener('scroll', function () {
      var scrollTop = window.pageYOffset || document.documentElement.scrollTop;
      var docHeight = document.documentElement.scrollHeight - window.innerHeight;
      var pct = docHeight > 0 ? (scrollTop / docHeight) * 100 : 0;
      bar.style.width = pct + '%';
    }, { passive: true });
  }

  /* ── Dark Mode Toggle ── */
  function initDarkMode() {
    var btn = document.getElementById('dark-mode-toggle');
    var smLight = document.getElementById('sm-theme-light');
    var smDark = document.getElementById('sm-theme-dark');
    var smSystem = document.getElementById('sm-theme-system');

    if (btn) {
      btn.addEventListener('click', function () {
        var isDark = document.body.classList.toggle('dark-mode');
        document.documentElement.classList.toggle('dark-mode', isDark);
        localStorage.setItem('pas_theme', isDark ? 'dark' : 'light');
        updateSmThemeBtns();
      });
    }

    if (smLight) {
      smLight.addEventListener('click', function () {
        document.body.classList.remove('dark-mode');
        document.documentElement.classList.remove('dark-mode');
        localStorage.setItem('pas_theme', 'light');
        updateSmThemeBtns();
      });
    }
    if (smDark) {
      smDark.addEventListener('click', function () {
        document.body.classList.add('dark-mode');
        document.documentElement.classList.add('dark-mode');
        localStorage.setItem('pas_theme', 'dark');
        updateSmThemeBtns();
      });
    }
    if (smSystem) {
      smSystem.addEventListener('click', function () {
        localStorage.removeItem('pas_theme');
        var prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
        document.body.classList.toggle('dark-mode', prefersDark);
        document.documentElement.classList.toggle('dark-mode', prefersDark);
        updateSmThemeBtns();
      });
    }

    updateSmThemeBtns();
  }

  function updateSmThemeBtns() {
    var saved = localStorage.getItem('pas_theme');
    var smLight = document.getElementById('sm-theme-light');
    var smDark = document.getElementById('sm-theme-dark');
    var smSystem = document.getElementById('sm-theme-system');
    [smLight, smDark, smSystem].forEach(function (b) { if (b) b.classList.remove('active'); });
    if (saved === 'light' && smLight) smLight.classList.add('active');
    else if (saved === 'dark' && smDark) smDark.classList.add('active');
    else if (!saved && smSystem) smSystem.classList.add('active');
  }

  /* ── Color Theme Picker ── */
  function initColorPicker() {
    var dots = document.querySelectorAll('.theme-dot');
    dots.forEach(function (dot) {
      dot.addEventListener('click', function () {
        dots.forEach(function (d) { d.classList.remove('active'); });
        dot.classList.add('active');
        var primary = dot.dataset.primary;
        var light = dot.dataset.light;
        var bg = dot.dataset.bg;
        var name = dot.title;
        applyColorTheme(primary, light, bg, name);
        localStorage.setItem('pas_color', JSON.stringify({ primary: primary, light: light, bg: bg, name: name }));
      });
    });
  }

  function applyColorTheme(primary, light, bg, name) {
    var root = document.documentElement;
    root.style.setProperty('--primary', primary);
    root.style.setProperty('--primary-light', light);
    root.style.setProperty('--primary-bg', bg);

    var shadowColor = hexToRgba(primary, 0.25);
    var shadowSm = hexToRgba(primary, 0.18);
    var shadowMd = hexToRgba(primary, 0.35);
    root.style.setProperty('--primary-shadow', shadowColor);
    root.style.setProperty('--primary-shadow-sm', shadowSm);
    root.style.setProperty('--primary-shadow-md', shadowMd);

    var dots = document.querySelectorAll('.theme-dot');
    dots.forEach(function (d) {
      d.classList.toggle('active', d.title === name);
    });
  }

  function hexToRgba(hex, alpha) {
    var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
    if (!result) return 'rgba(0,0,0,' + alpha + ')';
    var r = parseInt(result[1], 16);
    var g = parseInt(result[2], 16);
    var b = parseInt(result[3], 16);
    return 'rgba(' + r + ',' + g + ',' + b + ',' + alpha + ')';
  }

  /* ── Particle System ── */
  var particlesEnabled = true;
  var particleAnimId = null;
  var particles = [];

  function initParticles() {
    var canvas = document.getElementById('pas-particles');
    if (!canvas) return;
    var ctx = canvas.getContext('2d');

    function createParticle() {
      return {
        x: Math.random() * window.innerWidth,
        y: Math.random() * window.innerHeight,
        dx: (Math.random() - 0.5) * 0.8,
        dy: (Math.random() - 0.5) * 0.8,
        r: Math.random() * 2 + 1,
        alpha: Math.random() * 0.25 + 0.05
      };
    }

    var savedPart = localStorage.getItem('pas_particles');
    if (savedPart === 'off') {
      particlesEnabled = false;
      canvas.style.display = 'none';
      toggleParticleIcons(false);
    }

    var COUNT = Math.min(60, Math.floor((window.innerWidth * window.innerHeight) / 12000));
    for (var i = 0; i < COUNT; i++) {
      particles.push(createParticle());
    }

    function resize() {
      canvas.width = window.innerWidth;
      canvas.height = window.innerHeight;
      var newCount = Math.min(60, Math.floor((canvas.width * canvas.height) / 12000));
      while (particles.length < newCount) { particles.push(createParticle()); }
      while (particles.length > newCount) { particles.pop(); }
    }
    resize();
    window.addEventListener('resize', resize, { passive: true });

    function getPrimaryColor() {
      return getComputedStyle(document.documentElement).getPropertyValue('--primary').trim() || '#FF6A00';
    }

    function draw() {
      if (!particlesEnabled) return;
      ctx.clearRect(0, 0, canvas.width, canvas.height);
      var color = getPrimaryColor();
      particles.forEach(function (p) {
        p.x += p.dx;
        p.y += p.dy;
        if (p.x < 0 || p.x > canvas.width) p.dx *= -1;
        if (p.y < 0 || p.y > canvas.height) p.dy *= -1;
        ctx.beginPath();
        ctx.arc(p.x, p.y, p.r, 0, Math.PI * 2);
        ctx.fillStyle = color;
        ctx.globalAlpha = p.alpha;
        ctx.fill();
        ctx.globalAlpha = 1;
      });
      particleAnimId = requestAnimationFrame(draw);
    }

    if (particlesEnabled) draw();

    var toggleBtn = document.getElementById('particle-toggle');
    if (toggleBtn) {
      toggleBtn.addEventListener('click', function () {
        particlesEnabled = !particlesEnabled;
        if (particlesEnabled) {
          canvas.style.display = '';
          draw();
          localStorage.setItem('pas_particles', 'on');
        } else {
          cancelAnimationFrame(particleAnimId);
          ctx.clearRect(0, 0, canvas.width, canvas.height);
          canvas.style.display = 'none';
          localStorage.setItem('pas_particles', 'off');
        }
        toggleParticleIcons(particlesEnabled);
      });
    }
  }

  function toggleParticleIcons(on) {
    var iconOn = document.getElementById('particle-icon-on');
    var iconOff = document.getElementById('particle-icon-off');
    if (iconOn) iconOn.style.display = on ? '' : 'none';
    if (iconOff) iconOff.style.display = on ? 'none' : '';
  }

  /* ── Content Fold (About section) ── */
  function initContentFold() {
    var content = document.getElementById('sa-entry-content');
    var btn = document.getElementById('sa-show-more');
    if (!content || !btn) return;

    var fullHeight = content.scrollHeight;
    var collapsedHeight = Math.max(200, Math.round(fullHeight * 0.25));
    if (fullHeight <= collapsedHeight + 60) {
      btn.style.display = 'none';
      return;
    }

    content.style.maxHeight = collapsedHeight + 'px';
    content.classList.add('collapsed');

    btn.addEventListener('click', function () {
      var isCollapsed = content.classList.contains('collapsed');
      if (isCollapsed) {
        content.style.maxHeight = fullHeight + 'px';
        content.classList.remove('collapsed');
        btn.textContent = btn.dataset.hideLabel || 'Show less';
      } else {
        content.style.maxHeight = collapsedHeight + 'px';
        content.classList.add('collapsed');
        btn.textContent = btn.dataset.showLabel || 'Show more';
        content.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }
    });
  }

  /* ── Header Scroll Shadow ── */
  function initHeaderScroll() {
    var header = document.getElementById('site-header');
    if (!header) return;
    window.addEventListener('scroll', function () {
      header.classList.toggle('scrolled', window.pageYOffset > 10);
    }, { passive: true });
  }

  /* ── Search Dropdown ── */
  function initSearchToggle() {
    var btn = document.getElementById('search-toggle');
    var dropdown = document.getElementById('header-search');
    if (!btn || !dropdown) return;

    btn.addEventListener('click', function (e) {
      e.stopPropagation();
      var isVisible = dropdown.style.display !== 'none';
      dropdown.style.display = isVisible ? 'none' : 'block';
      if (!isVisible) {
        var input = dropdown.querySelector('input');
        if (input) setTimeout(function () { input.focus(); }, 50);
      }
    });

    document.addEventListener('click', function (e) {
      if (!dropdown.contains(e.target) && e.target !== btn) {
        dropdown.style.display = 'none';
      }
    });

    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') dropdown.style.display = 'none';
    });
  }

  /* ── Bottom Nav Active State ── */
  function initBottomNav() {
    var items = document.querySelectorAll('.nav-item[data-nav]');
    if (!items.length) return;
    var path = window.location.pathname;

    items.forEach(function (item) {
      var nav = item.dataset.nav;
      var active = false;
      if (nav === 'home' && (path === '/' || path === '')) active = true;
      else if (nav === 'apps' && path.indexOf('/apps') === 0) active = true;
      else if (nav === 'games' && path.indexOf('/games') === 0) active = true;
      else if (nav === 'category' && path.indexOf('/app-category') === 0 && path.indexOf('/games') === -1) active = true;
      if (active) item.classList.add('active');
    });
  }

  /* ── Slide Menu ── */
  function initSlideMenu() {
    var menu = document.getElementById('slide-menu');
    var overlay = document.getElementById('slide-menu-overlay');
    var closeBtn = document.getElementById('slide-menu-close');
    var openBtn = document.getElementById('menu-toggle-btn');
    if (!menu) return;

    function open() {
      menu.classList.add('open');
      menu.setAttribute('aria-hidden', 'false');
      document.body.style.overflow = 'hidden';
    }
    function close() {
      menu.classList.remove('open');
      menu.setAttribute('aria-hidden', 'true');
      document.body.style.overflow = '';
    }

    if (openBtn) openBtn.addEventListener('click', open);
    if (overlay) overlay.addEventListener('click', close);
    if (closeBtn) closeBtn.addEventListener('click', close);
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') close();
    });
  }

  /* ── Screenshots Carousel ── */
  function initScreenshotsCarousel() {
    var scroll = document.getElementById('sa-sc-scroll');
    var dotsWrap = document.getElementById('sa-sc-dots');
    if (!scroll || !dotsWrap) return;

    var dots = dotsWrap.querySelectorAll('.sa-sc-dot');
    var items = scroll.querySelectorAll('.sa-sc-item');
    if (!items.length || !dots.length) return;

    dots.forEach(function (dot, i) {
      dot.addEventListener('click', function () {
        var item = items[i];
        if (item) {
          scroll.scrollTo({ left: item.offsetLeft - scroll.offsetLeft, behavior: 'smooth' });
        }
      });
    });

    scroll.addEventListener('scroll', function () {
      var center = scroll.scrollLeft + scroll.clientWidth / 2;
      var closest = 0;
      var minDist = Infinity;
      items.forEach(function (item, i) {
        var itemCenter = item.offsetLeft + item.offsetWidth / 2;
        var dist = Math.abs(center - itemCenter);
        if (dist < minDist) { minDist = dist; closest = i; }
      });
      dots.forEach(function (d, i) { d.classList.toggle('active', i === closest); });
    }, { passive: true });
  }

  /* ── Sticky Bar ── */
  function initStickyBar() {
    var sticky = document.getElementById('sa-sticky');
    var info = document.querySelector('.sa-info-wrap');
    if (!sticky || !info) return;

    var observer = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        sticky.classList.toggle('visible', !entry.isIntersecting);
      });
    }, { threshold: 0 });
    observer.observe(info);
  }

  /* ── Tutorial Accordion ── */
  function initTutorialAccordion() {
    var tog = document.querySelector('.sa-tut-tog');
    var body = document.getElementById('sa-tut-body');
    if (!tog || !body) return;

    tog.addEventListener('click', function () {
      var expanded = tog.getAttribute('aria-expanded') === 'true';
      tog.setAttribute('aria-expanded', String(!expanded));
      body.classList.toggle('open', !expanded);
    });
  }

  /* ── YouTube Lazy Embed ── */
  function initYoutubeEmbed() {
    var playBtn = document.querySelector('.sa-tut-play');
    if (!playBtn) return;

    playBtn.addEventListener('click', function () {
      var ytId = playBtn.dataset.ytid;
      if (!ytId || !/^[A-Za-z0-9_-]{11}$/.test(ytId)) return;
      var thumb = document.getElementById('sa-tut-thumb');
      if (!thumb) return;
      var iframe = document.createElement('iframe');
      iframe.src = 'https://www.youtube-nocookie.com/embed/' + encodeURIComponent(ytId) + '?autoplay=1&rel=0';
      iframe.className = 'sa-tut-iframe';
      iframe.allow = 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture';
      iframe.allowFullscreen = true;
      iframe.title = 'YouTube video player';
      thumb.replaceWith(iframe);
    });
  }

  /* ── Share Button ── */
  function initShareButton() {
    var btn = document.getElementById('sa-share-btn');
    if (!btn) return;

    btn.addEventListener('click', function () {
      var data = {
        title: document.title,
        text: document.querySelector('meta[name="description"]') ? document.querySelector('meta[name="description"]').content : document.title,
        url: window.location.href
      };

      if (navigator.share) {
        navigator.share(data).catch(function () { fallbackShare(data.url); });
      } else {
        fallbackShare(data.url);
      }
    });
  }

  function fallbackShare(url) {
    if (navigator.clipboard) {
      navigator.clipboard.writeText(url).then(function () {
        showToast('Link copied!');
      }).catch(function () { legacyCopy(url); });
    } else {
      legacyCopy(url);
    }
  }

  function legacyCopy(text) {
    var el = document.createElement('textarea');
    el.value = text;
    el.style.position = 'fixed';
    el.style.left = '-9999px';
    document.body.appendChild(el);
    el.select();
    try { document.execCommand('copy'); showToast('Link copied!'); } catch (e) { /* ignore */ }
    document.body.removeChild(el);
  }

  function showToast(msg) {
    var toast = document.createElement('div');
    toast.textContent = msg;
    toast.style.cssText = 'position:fixed;bottom:calc(var(--nav-height,64px)+80px);left:50%;transform:translateX(-50%);background:var(--dark);color:var(--white);padding:10px 20px;border-radius:8px;font-size:0.85rem;font-weight:600;z-index:9999;pointer-events:none;transition:opacity 0.3s;';
    document.body.appendChild(toast);
    setTimeout(function () {
      toast.style.opacity = '0';
      setTimeout(function () { toast.remove(); }, 300);
    }, 2000);
  }

  /* ── Reveal Animations ── */
  function initRevealAnimations() {
    var els = document.querySelectorAll('.pas-reveal');
    if (!els.length) return;

    if ('IntersectionObserver' in window) {
      var observer = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
          if (entry.isIntersecting) {
            entry.target.classList.add('pas-visible');
            observer.unobserve(entry.target);
          }
        });
      }, { threshold: 0.08, rootMargin: '0px 0px -40px 0px' });

      els.forEach(function (el) { observer.observe(el); });
    } else {
      els.forEach(function (el) { el.classList.add('pas-visible'); });
    }
  }

  /* ── System Theme Preference ── */
  function initSystemTheme() {
    if (!window.matchMedia) return;
    var mq = window.matchMedia('(prefers-color-scheme: dark)');
    mq.addEventListener('change', function (e) {
      var saved = localStorage.getItem('pas_theme');
      if (!saved) {
        document.body.classList.toggle('dark-mode', e.matches);
        document.documentElement.classList.toggle('dark-mode', e.matches);
      }
    });
  }

})();
