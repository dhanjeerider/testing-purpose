<?php
require __DIR__ . '/includes/db.php';
require __DIR__ . '/includes/helpers.php';
require __DIR__ . '/includes/tmdb.php';
require __DIR__ . '/includes/layout.php';

$config = require __DIR__ . '/config.php';

$watchlistCount  = (int)$pdo->query('SELECT COUNT(*) FROM watchlist')->fetchColumn();
$moviesCount     = (int)$pdo->query("SELECT COUNT(*) FROM watchlist WHERE media_type='movie'")->fetchColumn();
$tvCount         = (int)$pdo->query("SELECT COUNT(*) FROM watchlist WHERE media_type='tv'")->fetchColumn();
$totalDbMovies   = (int)$pdo->query("SELECT COUNT(*) FROM movies WHERE is_active=1")->fetchColumn();
$payEnabled      = setting($pdo, 'subscription_enabled', '0') === '1';

// Trending recs
$trending = fetchTrending($config, 'all');

layout_head('Profile', $pdo);
layout_header($pdo);
?>

<main class="max-w-3xl mx-auto px-4 py-8 pb-12">

  <!-- Profile Header -->
  <div class="neu-raised rounded-2xl p-6 mb-6 text-center">
    <div class="w-20 h-20 mx-auto rounded-full bg-primary/20 flex items-center justify-center mb-3" style="border:3px solid rgba(0,229,255,0.3)">
      <?php if (isLoggedIn()): ?>
      <i class="fas fa-shield-alt text-3xl text-primary"></i>
      <?php else: ?>
      <i class="fas fa-user text-3xl text-white/30"></i>
      <?php endif; ?>
    </div>
    <h1 class="text-xl font-black text-white mb-1">
      <?= isLoggedIn() ? htmlspecialchars($_SESSION['admin_username'] ?? 'Admin', ENT_QUOTES, 'UTF-8') : 'Guest User' ?>
    </h1>
    <?php if (isLoggedIn()): ?>
    <span class="text-xs px-3 py-1 rounded-full bg-primary/15 text-primary font-semibold">
      <i class="fas fa-crown mr-1"></i>Administrator
    </span>
    <?php else: ?>
    <span class="text-xs text-white/40">Browse as guest</span>
    <?php endif; ?>

    <!-- Stats row -->
    <div class="grid grid-cols-3 gap-4 mt-5 pt-5 border-t border-white/10">
      <div class="text-center">
        <p class="text-2xl font-black text-primary"><?= $watchlistCount ?></p>
        <p class="text-xs text-white/40">Watchlist</p>
      </div>
      <div class="text-center">
        <p class="text-2xl font-black text-white"><?= $moviesCount ?></p>
        <p class="text-xs text-white/40">Movies</p>
      </div>
      <div class="text-center">
        <p class="text-2xl font-black text-white"><?= $tvCount ?></p>
        <p class="text-xs text-white/40">TV Shows</p>
      </div>
    </div>
  </div>

  <?php if (!isLoggedIn()): ?>
  <!-- Login form for guests -->
  <div class="neu-raised rounded-2xl p-6 mb-6">
    <h2 class="text-base font-bold text-white mb-4 flex items-center gap-2">
      <i class="fas fa-sign-in-alt text-primary"></i> Sign In
    </h2>
    <form method="post" action="actions.php">
      <input type="hidden" name="action" value="login">
      <div class="space-y-3">
        <div class="relative">
          <i class="fas fa-user absolute left-3 top-1/2 -translate-y-1/2 text-white/30 text-sm"></i>
          <input type="text" name="username" placeholder="Username" required class="input-dark pl-9 text-sm">
        </div>
        <div class="relative">
          <i class="fas fa-lock absolute left-3 top-1/2 -translate-y-1/2 text-white/30 text-sm"></i>
          <input type="password" name="password" placeholder="Password" required class="input-dark pl-9 text-sm">
        </div>
        <button type="submit" class="btn-primary w-full justify-center py-2.5 text-sm">
          <i class="fas fa-sign-in-alt"></i> Sign In as Admin
        </button>
      </div>
    </form>
  </div>
  <?php endif; ?>

  <!-- Subscription Card -->
  <?php if ($payEnabled): ?>
  <div class="neu-flat rounded-2xl p-5 mb-6" style="background:linear-gradient(135deg,rgba(0,229,255,0.05),rgba(255,62,141,0.05))">
    <div class="flex items-center justify-between gap-4">
      <div>
        <p class="font-bold text-white flex items-center gap-2"><i class="fas fa-crown text-yellow-400"></i> Premium Access</p>
        <p class="text-xs text-white/50 mt-0.5">Unlock ad-free streaming & exclusive content</p>
      </div>
      <a href="pricing.php" class="btn-accent text-sm py-2 px-4 flex-shrink-0">
        <i class="fas fa-arrow-right"></i> Subscribe
      </a>
    </div>
  </div>
  <?php endif; ?>

  <!-- Quick Links -->
  <div class="neu-flat rounded-2xl p-5 mb-6">
    <h2 class="text-base font-bold text-white mb-4 flex items-center gap-2">
      <i class="fas fa-th-large text-primary"></i> Quick Links
    </h2>
    <div class="grid grid-cols-2 gap-3">
      <a href="watchlist.php" class="flex items-center gap-3 p-3 rounded-xl hover:bg-white/5 transition-colors">
        <div class="w-9 h-9 rounded-lg bg-primary/15 flex items-center justify-center">
          <i class="fas fa-bookmark text-primary text-sm"></i>
        </div>
        <div>
          <p class="text-sm font-medium text-white">Watchlist</p>
          <p class="text-xs text-white/30"><?= $watchlistCount ?> saved</p>
        </div>
      </a>
      <a href="search.php" class="flex items-center gap-3 p-3 rounded-xl hover:bg-white/5 transition-colors">
        <div class="w-9 h-9 rounded-lg bg-accent/15 flex items-center justify-center">
          <i class="fas fa-search text-accent text-sm"></i>
        </div>
        <div>
          <p class="text-sm font-medium text-white">Search</p>
          <p class="text-xs text-white/30"><?= $totalDbMovies ?> titles</p>
        </div>
      </a>
      <a href="pricing.php" class="flex items-center gap-3 p-3 rounded-xl hover:bg-white/5 transition-colors">
        <div class="w-9 h-9 rounded-lg bg-yellow-400/15 flex items-center justify-center">
          <i class="fas fa-crown text-yellow-400 text-sm"></i>
        </div>
        <div>
          <p class="text-sm font-medium text-white">Pricing</p>
          <p class="text-xs text-white/30">Plans & features</p>
        </div>
      </a>
      <?php if (isLoggedIn()): ?>
      <a href="admin.php" class="flex items-center gap-3 p-3 rounded-xl hover:bg-white/5 transition-colors">
        <div class="w-9 h-9 rounded-lg bg-green-400/15 flex items-center justify-center">
          <i class="fas fa-cog text-green-400 text-sm"></i>
        </div>
        <div>
          <p class="text-sm font-medium text-white">Admin Panel</p>
          <p class="text-xs text-white/30">Manage content</p>
        </div>
      </a>
      <?php else: ?>
      <a href="upi-payment.php" class="flex items-center gap-3 p-3 rounded-xl hover:bg-white/5 transition-colors">
        <div class="w-9 h-9 rounded-lg bg-green-400/15 flex items-center justify-center">
          <i class="fas fa-rupee-sign text-green-400 text-sm"></i>
        </div>
        <div>
          <p class="text-sm font-medium text-white">UPI Payment</p>
          <p class="text-xs text-white/30">Pay & subscribe</p>
        </div>
      </a>
      <?php endif; ?>
    </div>
  </div>

  <!-- About / Info -->
  <div class="neu-flat rounded-2xl p-5 mb-6">
    <h2 class="text-base font-bold text-white mb-3 flex items-center gap-2">
      <i class="fas fa-info-circle text-primary"></i> About
    </h2>
    <p class="text-sm text-white/50 leading-relaxed">
      <?= htmlspecialchars(setting($pdo, 'site_description', 'Your ultimate destination for movies and TV shows.'), ENT_QUOTES, 'UTF-8') ?>
    </p>
    <div class="mt-4 pt-4 border-t border-white/5 flex items-center gap-2">
      <?php
      $socials = [
        ['telegram_link','fab fa-telegram','text-primary'],
        ['twitter_link','fab fa-twitter','text-blue-400'],
        ['instagram_link','fab fa-instagram','text-pink-400'],
        ['youtube_link','fab fa-youtube','text-red-400'],
      ];
      foreach ($socials as [$key,$icon,$color]):
        $link = setting($pdo, $key, '');
        if ($link):
      ?>
      <a href="<?= htmlspecialchars($link, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener"
         class="w-9 h-9 rounded-full neu-raised flex items-center justify-center <?= $color ?> hover:opacity-80 transition-opacity">
        <i class="<?= $icon ?> text-sm"></i>
      </a>
      <?php endif; endforeach; ?>
    </div>
  </div>

  <!-- Trending Recs -->
  <?php if (!empty($trending)): ?>
  <div>
    <h2 class="section-title mb-3">Recommended For You</h2>
    <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-3">
      <?php foreach (array_slice($trending, 0, 10) as $t): ?>
      <?php
      $tId   = (int)$t['id'];
      $tType = $t['media_type'] ?? 'movie';
      $tTitle= $t['title'] ?? $t['name'] ?? 'Untitled';
      ?>
      <div>
        <a href="watch.php?type=<?= htmlspecialchars($tType, ENT_QUOTES, 'UTF-8') ?>&id=<?= $tId ?>" class="movie-card block">
          <img src="<?= htmlspecialchars(posterUrl($t['poster_path'] ?? null), ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($tTitle, ENT_QUOTES, 'UTF-8') ?>" loading="lazy">
          <div class="overlay"></div>
          <div class="play-btn"><i class="fas fa-play text-sm"></i></div>
          <?php if ($t['vote_average'] > 0): ?><div class="rating"><i class="fas fa-star text-yellow-400 text-xs"></i><?= number_format((float)$t['vote_average'], 1) ?></div><?php endif; ?>
        </a>
        <p class="text-xs text-white/60 mt-1 truncate"><?= htmlspecialchars($tTitle, ENT_QUOTES, 'UTF-8') ?></p>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endif; ?>

  <?php if (isLoggedIn()): ?>
  <div class="mt-6 text-center">
    <form method="post" action="actions.php">
      <input type="hidden" name="action" value="logout">
      <button type="submit" class="btn-outline py-2.5 px-6 text-sm">
        <i class="fas fa-sign-out-alt"></i> Logout
      </button>
    </form>
  </div>
  <?php endif; ?>

</main>

<?php layout_footer($pdo); ?>
