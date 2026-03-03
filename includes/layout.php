<?php
// includes/layout.php – shared layout functions

function layout_head(string $title, PDO $pdo): void {
    $siteTitle = setting($pdo, 'site_title', 'TMovie PHP');
    $siteDesc  = setting($pdo, 'site_description', 'Watch trending movies and TV shows');
    $gaId      = setting($pdo, 'google_analytics_id', '');
    $scMeta    = setting($pdo, 'search_console_meta', '');
    $logoUrl   = setting($pdo, 'logo_url', '');
    $faviconUrl= setting($pdo, 'favicon_url', '');
    $keywords  = setting($pdo, 'keywords', 'movies, tv shows, streaming, watch online');
    $ogImage   = setting($pdo, 'og_image', '');
    $fullTitle = $title ? htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . ' – ' . htmlspecialchars($siteTitle, ENT_QUOTES, 'UTF-8') : htmlspecialchars($siteTitle, ENT_QUOTES, 'UTF-8');
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= $fullTitle ?></title>
<meta name="description" content="<?= htmlspecialchars($siteDesc, ENT_QUOTES, 'UTF-8') ?>">
<meta name="keywords" content="<?= htmlspecialchars($keywords, ENT_QUOTES, 'UTF-8') ?>">
<meta property="og:title" content="<?= $fullTitle ?>">
<meta property="og:description" content="<?= htmlspecialchars($siteDesc, ENT_QUOTES, 'UTF-8') ?>">
<?php if ($ogImage): ?><meta property="og:image" content="<?= htmlspecialchars($ogImage, ENT_QUOTES, 'UTF-8') ?>"><?php endif; ?>
<?php if ($faviconUrl): ?><link rel="icon" href="<?= htmlspecialchars($faviconUrl, ENT_QUOTES, 'UTF-8') ?>"><?php endif; ?>
<?php if ($scMeta): ?><?= $scMeta ?><?php endif; ?>
<script>
tailwind.config = {
    theme: { extend: { colors: { primary: '#00e5ff', accent: '#ff3e8d' } } }
}
</script>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
:root { --primary: #00e5ff; --accent: #ff3e8d; --bg: #1a1d2e; }
*, *::before, *::after { box-sizing: border-box; }
html { font-family: Inter, system-ui, -apple-system, sans-serif; }
body { background-color: #1a1d2e; color: rgba(255,255,255,0.87); margin: 0; }
a { text-decoration: none; color: inherit; }
.neu-raised { background: linear-gradient(145deg, #1e2237, #171a2a); box-shadow: 5px 5px 12px #0c0e1d, -5px -5px 12px #272c44; }
.neu-flat { background: linear-gradient(145deg, #1e2237, #171a2a); box-shadow: 3px 3px 8px #0c0e1d, -3px -3px 8px #272c44; }
.neu-pressed { background: #1a1d2e; box-shadow: inset 3px 3px 8px #0c0e1d, inset -3px -3px 8px #272c44; }
.text-primary { color: #00e5ff !important; }
.text-accent { color: #ff3e8d !important; }
.text-muted { color: rgba(255,255,255,0.5); }
.border-primary { border-color: #00e5ff !important; }
.bg-primary { background-color: #00e5ff !important; }
.bg-accent { background-color: #ff3e8d !important; }
.btn-primary { background: #00e5ff; color: #000; font-weight: 700; padding: 0.625rem 1.25rem; border-radius: 0.5rem; border: none; cursor: pointer; display: inline-flex; align-items: center; gap: 0.4rem; transition: opacity .2s; }
.btn-primary:hover { opacity: 0.9; }
.btn-accent { background: #ff3e8d; color: #fff; font-weight: 700; padding: 0.625rem 1.25rem; border-radius: 0.5rem; border: none; cursor: pointer; display: inline-flex; align-items: center; gap: 0.4rem; transition: opacity .2s; }
.btn-accent:hover { opacity: 0.9; }
.btn-outline { background: transparent; color: rgba(255,255,255,0.8); font-weight: 600; padding: 0.6rem 1.2rem; border-radius: 0.5rem; border: 1px solid rgba(255,255,255,0.25); cursor: pointer; display: inline-flex; align-items: center; gap: 0.4rem; transition: all .2s; }
.btn-outline:hover { border-color: #00e5ff; color: #00e5ff; }
.movie-card { position: relative; border-radius: 0.5rem; overflow: hidden; cursor: pointer; }
.movie-card img { width: 100%; aspect-ratio: 2/3; object-fit: cover; display: block; }
.movie-card .overlay { position: absolute; inset: 0; background: linear-gradient(to top, rgba(0,0,0,0.7), transparent); opacity: 0; transition: opacity 0.3s; }
.movie-card:hover .overlay { opacity: 1; }
.movie-card .play-btn { position: absolute; top: 50%; left: 50%; transform: translate(-50%,-50%); width: 48px; height: 48px; border-radius: 50%; border: 2px solid #00e5ff; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; opacity: 0; transition: opacity 0.3s; color: #00e5ff; }
.movie-card:hover .play-btn { opacity: 1; }
.movie-card .rating { position: absolute; top: 6px; right: 6px; background: rgba(0,0,0,0.7); border-radius: 4px; padding: 2px 6px; font-size: 11px; color: rgba(255,255,255,0.9); display: flex; align-items: center; gap: 3px; }
.movie-card .badge-type { position: absolute; top: 6px; left: 6px; background: rgba(0,0,0,0.7); border-radius: 4px; padding: 2px 6px; font-size: 10px; color: #00e5ff; text-transform: uppercase; font-weight: 700; }
.content-row { overflow-x: auto; display: flex; gap: 10px; padding-bottom: 8px; scrollbar-width: none; }
.content-row::-webkit-scrollbar { display: none; }
.content-row .card-item { flex: 0 0 140px; }
@media (min-width: 640px) { .content-row .card-item { flex: 0 0 160px; } }
.hero-slide { position: relative; }
.input-dark { background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); color: white; padding: 0.625rem 0.875rem; border-radius: 0.5rem; outline: none; width: 100%; transition: border-color .2s; }
.input-dark:focus { border-color: #00e5ff; }
.input-dark::placeholder { color: rgba(255,255,255,0.35); }
.tab-btn { padding: 0.75rem 1.5rem; font-size: 0.875rem; font-weight: 500; border: none; background: transparent; cursor: pointer; color: rgba(255,255,255,0.5); border-bottom: 2px solid transparent; transition: all 0.2s; white-space: nowrap; }
.tab-btn.active { color: #00e5ff; border-bottom-color: #00e5ff; }
.tab-btn:hover { color: white; }
.site-header { position: fixed; top: 0; left: 0; right: 0; z-index: 50; transition: transform .3s; }
.site-header.hidden-header { transform: translateY(-100%); }
.bottom-nav { position: fixed; bottom: 0; left: 0; right: 0; z-index: 50; display: none; }
@media (max-width: 767px) { .bottom-nav { display: flex; } }
.section-title { font-size: 1.125rem; font-weight: 700; color: white; display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.75rem; }
.section-title::before { content: ''; display: block; width: 4px; height: 18px; background: #00e5ff; border-radius: 2px; }
.alert { padding: 0.75rem 1rem; border-radius: 0.5rem; font-size: 0.875rem; margin-bottom: 1rem; }
.alert-success { background: rgba(0,229,255,0.1); border: 1px solid rgba(0,229,255,0.3); color: #00e5ff; }
.alert-error { background: rgba(255,62,141,0.1); border: 1px solid rgba(255,62,141,0.3); color: #ff3e8d; }
.alert-info { background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.15); color: rgba(255,255,255,0.8); }
select.input-dark option { background: #1e2237; }
</style>
<?php if ($gaId): ?>
<script async src="https://www.googletagmanager.com/gtag/js?id=<?= htmlspecialchars($gaId, ENT_QUOTES, 'UTF-8') ?>"></script>
<script>
window.dataLayer = window.dataLayer || [];
function gtag(){dataLayer.push(arguments);}
gtag('js', new Date());
gtag('config', '<?= htmlspecialchars($gaId, ENT_QUOTES, 'UTF-8') ?>');
</script>
<?php endif; ?>
</head>
<body class="pb-16 md:pb-0">
<?php
}

function layout_header(PDO $pdo): void {
    $siteTitle   = setting($pdo, 'site_title', 'TMovie PHP');
    $logoUrl     = setting($pdo, 'logo_url', '');
    $currentFile = basename($_SERVER['PHP_SELF'] ?? 'index.php');
    $isAdmin     = isLoggedIn();
    ?>
<!-- HEADER -->
<header class="site-header bg-gradient-to-b from-[#1e2237] to-[#1a1d2e] border-b border-white/5" id="siteHeader">
  <div class="max-w-7xl mx-auto px-4 h-16 flex items-center justify-between gap-4">
    <!-- Logo -->
    <a href="index.php" class="flex items-center gap-2 flex-shrink-0">
      <?php if ($logoUrl): ?>
        <img src="<?= htmlspecialchars($logoUrl, ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($siteTitle, ENT_QUOTES, 'UTF-8') ?>" class="h-8 w-auto">
      <?php else: ?>
        <span class="text-xl font-black text-white">T<span class="text-primary">movie</span></span>
      <?php endif; ?>
    </a>

    <!-- Desktop Nav -->
    <nav class="hidden md:flex items-center gap-1">
      <a href="index.php" class="px-3 py-2 rounded-lg text-sm font-medium transition-colors <?= $currentFile === 'index.php' ? 'text-primary' : 'text-white/60 hover:text-white' ?>">
        <i class="fas fa-home mr-1"></i>Home
      </a>
      <a href="search.php" class="px-3 py-2 rounded-lg text-sm font-medium transition-colors <?= $currentFile === 'search.php' ? 'text-primary' : 'text-white/60 hover:text-white' ?>">
        <i class="fas fa-search mr-1"></i>Search
      </a>
      <a href="watchlist.php" class="px-3 py-2 rounded-lg text-sm font-medium transition-colors <?= $currentFile === 'watchlist.php' ? 'text-primary' : 'text-white/60 hover:text-white' ?>">
        <i class="fas fa-bookmark mr-1"></i>Watchlist
      </a>
      <a href="profile.php" class="px-3 py-2 rounded-lg text-sm font-medium transition-colors <?= $currentFile === 'profile.php' ? 'text-primary' : 'text-white/60 hover:text-white' ?>">
        <i class="fas fa-user mr-1"></i>Profile
      </a>
      <?php if ($isAdmin): ?>
      <a href="admin.php" class="px-3 py-2 rounded-lg text-sm font-medium transition-colors <?= $currentFile === 'admin.php' ? 'text-primary' : 'text-white/60 hover:text-white' ?>">
        <i class="fas fa-cog mr-1"></i>Admin
      </a>
      <?php endif; ?>
    </nav>

    <!-- Right icons -->
    <div class="flex items-center gap-2">
      <a href="search.php" class="md:hidden p-2 text-white/70 hover:text-white transition-colors">
        <i class="fas fa-search text-lg"></i>
      </a>
      <?php if ($isAdmin): ?>
      <a href="admin.php" class="hidden md:flex items-center gap-1.5 btn-primary text-sm py-2 px-3 rounded-lg">
        <i class="fas fa-shield-alt"></i><span>Admin</span>
      </a>
      <form method="post" action="actions.php" class="hidden md:block">
        <input type="hidden" name="action" value="logout">
        <button type="submit" class="btn-outline text-sm py-2 px-3 rounded-lg">
          <i class="fas fa-sign-out-alt"></i>
        </button>
      </form>
      <?php else: ?>
      <a href="login.php" class="hidden md:flex items-center gap-1.5 btn-outline text-sm py-2 px-3 rounded-lg">
        <i class="fas fa-sign-in-alt"></i><span>Login</span>
      </a>
      <?php endif; ?>
      <!-- Hamburger -->
      <button id="hamburgerBtn" class="md:hidden p-2 text-white/70 hover:text-white transition-colors" aria-label="Menu">
        <i class="fas fa-bars text-lg"></i>
      </button>
    </div>
  </div>
</header>

<!-- Mobile slide-in menu -->
<div id="mobileMenuOverlay" class="fixed inset-0 bg-black/60 z-40 hidden" onclick="closeMobileMenu()"></div>
<div id="mobileMenu" class="fixed top-0 right-0 bottom-0 w-[67%] max-w-xs z-50 neu-raised flex flex-col translate-x-full transition-transform duration-300" style="padding-top:env(safe-area-inset-top)">
  <div class="flex items-center justify-between p-4 border-b border-white/10">
    <span class="font-bold text-white">Menu</span>
    <button onclick="closeMobileMenu()" class="p-1 text-white/60 hover:text-white"><i class="fas fa-times text-xl"></i></button>
  </div>
  <nav class="flex-1 overflow-y-auto py-4 flex flex-col gap-1 px-3">
    <a href="index.php" class="flex items-center gap-3 px-3 py-3 rounded-lg <?= $currentFile === 'index.php' ? 'bg-primary/10 text-primary' : 'text-white/70 hover:text-white hover:bg-white/5' ?> transition-colors">
      <i class="fas fa-home w-5 text-center"></i><span>Home</span>
    </a>
    <a href="search.php" class="flex items-center gap-3 px-3 py-3 rounded-lg <?= $currentFile === 'search.php' ? 'bg-primary/10 text-primary' : 'text-white/70 hover:text-white hover:bg-white/5' ?> transition-colors">
      <i class="fas fa-search w-5 text-center"></i><span>Search</span>
    </a>
    <a href="watchlist.php" class="flex items-center gap-3 px-3 py-3 rounded-lg <?= $currentFile === 'watchlist.php' ? 'bg-primary/10 text-primary' : 'text-white/70 hover:text-white hover:bg-white/5' ?> transition-colors">
      <i class="fas fa-bookmark w-5 text-center"></i><span>Watchlist</span>
    </a>
    <a href="profile.php" class="flex items-center gap-3 px-3 py-3 rounded-lg <?= $currentFile === 'profile.php' ? 'bg-primary/10 text-primary' : 'text-white/70 hover:text-white hover:bg-white/5' ?> transition-colors">
      <i class="fas fa-user w-5 text-center"></i><span>Profile</span>
    </a>
    <a href="pricing.php" class="flex items-center gap-3 px-3 py-3 rounded-lg <?= $currentFile === 'pricing.php' ? 'bg-primary/10 text-primary' : 'text-white/70 hover:text-white hover:bg-white/5' ?> transition-colors">
      <i class="fas fa-crown w-5 text-center"></i><span>Pricing</span>
    </a>
    <?php if ($isAdmin): ?>
    <a href="admin.php" class="flex items-center gap-3 px-3 py-3 rounded-lg <?= $currentFile === 'admin.php' ? 'bg-primary/10 text-primary' : 'text-white/70 hover:text-white hover:bg-white/5' ?> transition-colors">
      <i class="fas fa-cog w-5 text-center"></i><span>Admin</span>
    </a>
    <form method="post" action="actions.php" class="mt-2">
      <input type="hidden" name="action" value="logout">
      <button type="submit" class="w-full flex items-center gap-3 px-3 py-3 rounded-lg text-white/70 hover:text-white hover:bg-white/5 transition-colors text-left">
        <i class="fas fa-sign-out-alt w-5 text-center"></i><span>Logout</span>
      </button>
    </form>
    <?php else: ?>
    <a href="login.php" class="flex items-center gap-3 px-3 py-3 rounded-lg text-white/70 hover:text-white hover:bg-white/5 transition-colors">
      <i class="fas fa-sign-in-alt w-5 text-center"></i><span>Login</span>
    </a>
    <?php endif; ?>
  </nav>
</div>

<!-- Mobile bottom nav -->
<div class="bottom-nav neu-flat border-t border-white/5 justify-around py-2 pb-safe" style="padding-bottom: max(0.5rem, env(safe-area-inset-bottom))">
  <a href="index.php" class="flex flex-col items-center gap-0.5 py-1 px-4 <?= $currentFile === 'index.php' ? 'text-primary' : 'text-white/50' ?> text-xs font-medium transition-colors">
    <i class="fas fa-home text-xl"></i><span>Home</span>
  </a>
  <a href="search.php" class="flex flex-col items-center gap-0.5 py-1 px-4 <?= $currentFile === 'search.php' ? 'text-primary' : 'text-white/50' ?> text-xs font-medium transition-colors">
    <i class="fas fa-search text-xl"></i><span>Search</span>
  </a>
  <a href="watchlist.php" class="flex flex-col items-center gap-0.5 py-1 px-4 <?= $currentFile === 'watchlist.php' ? 'text-primary' : 'text-white/50' ?> text-xs font-medium transition-colors">
    <i class="fas fa-bookmark text-xl"></i><span>Saved</span>
  </a>
  <a href="profile.php" class="flex flex-col items-center gap-0.5 py-1 px-4 <?= $currentFile === 'profile.php' ? 'text-primary' : 'text-white/50' ?> text-xs font-medium transition-colors">
    <i class="fas fa-user text-xl"></i><span>Profile</span>
  </a>
</div>

<!-- Spacer for fixed header -->
<div class="h-16"></div>

<script>
(function(){
  // Auto-hide header on scroll
  var header = document.getElementById('siteHeader');
  var lastScroll = 0;
  window.addEventListener('scroll', function(){
    var current = window.scrollY;
    if (current > 80 && current > lastScroll) {
      header.classList.add('hidden-header');
    } else {
      header.classList.remove('hidden-header');
    }
    lastScroll = current;
  }, { passive: true });

  // Mobile menu
  var btn = document.getElementById('hamburgerBtn');
  var menu = document.getElementById('mobileMenu');
  var overlay = document.getElementById('mobileMenuOverlay');
  if (btn) btn.addEventListener('click', function(){ openMobileMenu(); });
})();

function openMobileMenu(){
  document.getElementById('mobileMenu').style.transform = 'translateX(0)';
  document.getElementById('mobileMenuOverlay').classList.remove('hidden');
  document.body.style.overflow = 'hidden';
}
function closeMobileMenu(){
  document.getElementById('mobileMenu').style.transform = '';
  document.getElementById('mobileMenuOverlay').classList.add('hidden');
  document.body.style.overflow = '';
}
</script>
<?php
}

function layout_footer(PDO $pdo): void {
    $siteTitle    = setting($pdo, 'site_title', 'TMovie PHP');
    $footerText   = setting($pdo, 'footer_text', '');
    $telegramLink = setting($pdo, 'telegram_link', '');
    $twitterLink  = setting($pdo, 'twitter_link', '');
    $instagramLink= setting($pdo, 'instagram_link', '');
    $youtubeLink  = setting($pdo, 'youtube_link', '');
    $facebookLink = setting($pdo, 'facebook_link', '');
    $year         = date('Y');

    $footerPages = $pdo->query('SELECT id, title, slug FROM pages WHERE is_active=1 AND show_in_footer=1 ORDER BY sort_order ASC, id ASC')->fetchAll();
    ?>
<footer class="mt-16 border-t border-white/10 bg-gradient-to-b from-[#1a1d2e] to-[#141624]">
  <div class="max-w-7xl mx-auto px-4 py-10">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">
      <!-- Brand -->
      <div>
        <a href="index.php" class="text-xl font-black text-white mb-3 inline-block">T<span class="text-primary">movie</span></a>
        <p class="text-sm text-white/50 leading-relaxed"><?= htmlspecialchars($footerText ?: 'Your ultimate destination for movies and TV shows.', ENT_QUOTES, 'UTF-8') ?></p>
      </div>
      <!-- Links -->
      <?php if ($footerPages): ?>
      <div>
        <h4 class="text-sm font-semibold text-white/70 uppercase tracking-wider mb-3">Pages</h4>
        <ul class="space-y-1.5">
          <?php foreach ($footerPages as $fp): ?>
          <li><a href="page.php?slug=<?= htmlspecialchars($fp['slug'], ENT_QUOTES, 'UTF-8') ?>" class="text-sm text-white/50 hover:text-primary transition-colors"><?= htmlspecialchars($fp['title'], ENT_QUOTES, 'UTF-8') ?></a></li>
          <?php endforeach; ?>
        </ul>
      </div>
      <?php endif; ?>
      <!-- Social -->
      <div>
        <h4 class="text-sm font-semibold text-white/70 uppercase tracking-wider mb-3">Follow Us</h4>
        <div class="flex items-center gap-3 flex-wrap">
          <?php if ($telegramLink): ?><a href="<?= htmlspecialchars($telegramLink, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener" class="w-9 h-9 rounded-full neu-flat flex items-center justify-center text-white/60 hover:text-primary transition-colors"><i class="fab fa-telegram"></i></a><?php endif; ?>
          <?php if ($twitterLink): ?><a href="<?= htmlspecialchars($twitterLink, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener" class="w-9 h-9 rounded-full neu-flat flex items-center justify-center text-white/60 hover:text-primary transition-colors"><i class="fab fa-twitter"></i></a><?php endif; ?>
          <?php if ($instagramLink): ?><a href="<?= htmlspecialchars($instagramLink, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener" class="w-9 h-9 rounded-full neu-flat flex items-center justify-center text-white/60 hover:text-primary transition-colors"><i class="fab fa-instagram"></i></a><?php endif; ?>
          <?php if ($youtubeLink): ?><a href="<?= htmlspecialchars($youtubeLink, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener" class="w-9 h-9 rounded-full neu-flat flex items-center justify-center text-white/60 hover:text-primary transition-colors"><i class="fab fa-youtube"></i></a><?php endif; ?>
          <?php if ($facebookLink): ?><a href="<?= htmlspecialchars($facebookLink, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener" class="w-9 h-9 rounded-full neu-flat flex items-center justify-center text-white/60 hover:text-primary transition-colors"><i class="fab fa-facebook"></i></a><?php endif; ?>
        </div>
      </div>
    </div>
    <div class="border-t border-white/5 pt-6 flex flex-col md:flex-row items-center justify-between gap-3 text-xs text-white/30">
      <p>&copy; <?= $year ?> <?= htmlspecialchars($siteTitle, ENT_QUOTES, 'UTF-8') ?>. All rights reserved.</p>
      <div class="flex items-center gap-4">
        <a href="pricing.php" class="hover:text-white/60 transition-colors">Pricing</a>
        <a href="watchlist.php" class="hover:text-white/60 transition-colors">Watchlist</a>
        <a href="search.php" class="hover:text-white/60 transition-colors">Search</a>
      </div>
    </div>
  </div>
</footer>
</body>
</html>
<?php
}
