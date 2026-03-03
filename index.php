<?php
require __DIR__ . '/includes/db.php';
require __DIR__ . '/includes/helpers.php';
require __DIR__ . '/includes/tmdb.php';
require __DIR__ . '/includes/layout.php';

$config   = require __DIR__ . '/config.php';

// Fetch site settings
$siteTelegramLink = setting($pdo, 'telegram_link', '');

// Fetch DB movies (featured first)
$dbMovies = $pdo->query('SELECT * FROM movies WHERE is_active=1 ORDER BY is_featured DESC, id DESC LIMIT 60')->fetchAll();

// Fetch watchlist
$watchlistItems = $pdo->query('SELECT * FROM watchlist ORDER BY created_at DESC LIMIT 20')->fetchAll();
$watchlistIds   = array_column($watchlistItems, null, 'tmdb_id');

// Fetch widgets
$widgets = $pdo->query('SELECT * FROM widgets WHERE is_active=1 ORDER BY sort_order ASC, id ASC')->fetchAll();

// Fetch trending from TMDB
$trendingAll    = fetchTrending($config, 'all');
$trendingMovies = fetchTrending($config, 'movie');
$trendingTV     = fetchTrending($config, 'tv');

// Hero: use featured DB movies if available, else trending
$heroItems = [];
$featuredDb = array_filter($dbMovies, fn($m) => (int)$m['is_featured'] === 1);
if (count($featuredDb) >= 3) {
    foreach (array_slice(array_values($featuredDb), 0, 5) as $m) {
        $heroItems[] = [
            'tmdb_id'    => $m['tmdb_id'],
            'media_type' => $m['media_type'],
            'title'      => $m['title'],
            'overview'   => $m['overview'] ?? '',
            'backdrop'   => backdropUrl($m['backdrop_path']),
            'rating'     => $m['vote_average'],
        ];
    }
} elseif (!empty($trendingAll)) {
    foreach (array_slice($trendingAll, 0, 5) as $t) {
        $heroItems[] = [
            'tmdb_id'    => $t['id'],
            'media_type' => $t['media_type'] ?? 'movie',
            'title'      => $t['title'] ?? $t['name'] ?? 'Untitled',
            'overview'   => $t['overview'] ?? '',
            'backdrop'   => backdropUrl($t['backdrop_path'] ?? ''),
            'rating'     => $t['vote_average'] ?? 0,
        ];
    }
} elseif (!empty($dbMovies)) {
    foreach (array_slice($dbMovies, 0, 5) as $m) {
        $heroItems[] = [
            'tmdb_id'    => $m['tmdb_id'],
            'media_type' => $m['media_type'],
            'title'      => $m['title'],
            'overview'   => $m['overview'] ?? '',
            'backdrop'   => backdropUrl($m['backdrop_path']),
            'rating'     => $m['vote_average'],
        ];
    }
}

layout_head('Home', $pdo);
layout_header($pdo);

// Helper: render a movie card
function renderCard(array $item, string $type, int $tmdbId, ?string $poster, string $title, float $rating = 0): void {
    $url = 'watch.php?type=' . htmlspecialchars($type, ENT_QUOTES, 'UTF-8') . '&id=' . $tmdbId;
    echo '<div class="card-item">';
    echo '<a href="' . $url . '" class="movie-card block">';
    echo '<img src="' . htmlspecialchars(posterUrl($poster), ENT_QUOTES, 'UTF-8') . '" alt="' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '" loading="lazy">';
    echo '<div class="overlay"></div>';
    echo '<div class="play-btn"><i class="fas fa-play text-sm"></i></div>';
    if ($rating > 0) echo '<div class="rating"><i class="fas fa-star text-yellow-400 text-xs"></i> ' . number_format((float)$rating, 1) . '</div>';
    echo '<div class="badge-type">' . htmlspecialchars($type === 'tv' ? 'TV' : 'Movie', ENT_QUOTES, 'UTF-8') . '</div>';
    echo '</a>';
    echo '<p class="text-xs text-white/70 mt-1 truncate leading-tight px-0.5">' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '</p>';
    echo '</div>';
}
?>

<!-- HERO SLIDER -->
<?php if (!empty($heroItems)): ?>
<section class="relative overflow-hidden" style="height: min(75vh, 560px);" id="heroSection">
  <div id="heroSlides" class="flex h-full transition-transform duration-500">
    <?php foreach ($heroItems as $i => $h): ?>
    <div class="hero-slide flex-shrink-0 w-full h-full relative" style="min-width:100%">
      <img src="<?= htmlspecialchars($h['backdrop'], ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($h['title'], ENT_QUOTES, 'UTF-8') ?>"
           class="w-full h-full object-cover absolute inset-0" loading="<?= $i === 0 ? 'eager' : 'lazy' ?>">
      <div class="absolute inset-0" style="background:linear-gradient(to right,rgba(26,29,46,0.95) 35%,rgba(26,29,46,0.3) 70%,transparent 100%)"></div>
      <div class="absolute inset-0" style="background:linear-gradient(to top,rgba(26,29,46,1) 0%,transparent 40%)"></div>
      <div class="relative h-full flex flex-col justify-end px-6 pb-16 md:pb-12 max-w-2xl">
        <div class="flex items-center gap-2 mb-2">
          <span class="text-xs font-bold px-2 py-0.5 rounded bg-primary/20 text-primary uppercase tracking-wider"><?= $h['media_type'] === 'tv' ? 'TV Series' : 'Movie' ?></span>
          <?php if ($h['rating'] > 0): ?>
          <span class="text-xs text-white/70 flex items-center gap-1"><i class="fas fa-star text-yellow-400 text-xs"></i><?= number_format((float)$h['rating'], 1) ?></span>
          <?php endif; ?>
        </div>
        <h1 class="text-2xl md:text-4xl font-black text-white mb-2 leading-tight"><?= htmlspecialchars($h['title'], ENT_QUOTES, 'UTF-8') ?></h1>
        <?php if ($h['overview']): ?>
        <p class="text-sm text-white/60 leading-relaxed mb-4 line-clamp-2 max-w-lg"><?= htmlspecialchars(mb_substr($h['overview'], 0, 180), ENT_QUOTES, 'UTF-8') ?>...</p>
        <?php endif; ?>
        <div class="flex items-center gap-3">
          <a href="watch.php?type=<?= htmlspecialchars($h['media_type'], ENT_QUOTES, 'UTF-8') ?>&id=<?= (int)$h['tmdb_id'] ?>" class="btn-primary">
            <i class="fas fa-play"></i> Watch Now
          </a>
          <a href="watch.php?type=<?= htmlspecialchars($h['media_type'], ENT_QUOTES, 'UTF-8') ?>&id=<?= (int)$h['tmdb_id'] ?>" class="btn-outline">
            <i class="fas fa-info-circle"></i> More Info
          </a>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
  <!-- Indicators -->
  <?php if (count($heroItems) > 1): ?>
  <div class="absolute bottom-5 left-6 flex gap-2" id="heroIndicators">
    <?php foreach ($heroItems as $i => $_): ?>
    <button onclick="goHeroSlide(<?= $i ?>)" class="h-1 rounded-full transition-all duration-300 <?= $i === 0 ? 'w-8 bg-primary' : 'w-3 bg-white/30' ?>" data-idx="<?= $i ?>"></button>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
</section>
<script>
(function(){
  var current = 0;
  var total = <?= count($heroItems) ?>;
  var track = document.getElementById('heroSlides');
  var indicators = document.querySelectorAll('#heroIndicators button');
  var timer;
  function go(idx){
    current = idx;
    track.style.transform = 'translateX(-' + (idx * 100) + '%)';
    indicators.forEach(function(b, i){
      b.className = 'h-1 rounded-full transition-all duration-300 ' + (i === idx ? 'w-8 bg-primary' : 'w-3 bg-white/30');
    });
  }
  window.goHeroSlide = go;
  function autoPlay(){ timer = setInterval(function(){ go((current + 1) % total); }, 5000); }
  autoPlay();
  track.addEventListener('mouseenter', function(){ clearInterval(timer); });
  track.addEventListener('mouseleave', function(){ autoPlay(); });
})();
</script>
<?php endif; ?>

<!-- OTT PROVIDER STRIP -->
<section class="py-4 border-b border-white/5">
  <div class="max-w-7xl mx-auto px-4">
    <div class="overflow-x-auto flex items-center gap-3" style="scrollbar-width:none">
      <?php
      $otts = [
        ['Netflix','#e50914'],['Prime Video','#00a8e0'],['Disney+','#113ccf'],['Hotstar','#1f80e0'],
        ['ZEE5','#9b59b6'],['SonyLIV','#0057ff'],['Hulu','#1ce783'],['Apple TV+','#555'],
        ['Peacock','#5b40ba'],['Paramount+','#0064ff']
      ];
      foreach ($otts as [$name, $color]):
      ?>
      <div class="flex-shrink-0 px-3 py-1.5 rounded-lg text-xs font-bold whitespace-nowrap" style="background:<?= $color ?>22;border:1px solid <?= $color ?>44;color:<?= $color ?>">
        <?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- TELEGRAM CTA BANNER -->
<?php if ($siteTelegramLink): ?>
<section class="py-5">
  <div class="max-w-7xl mx-auto px-4">
    <div class="neu-flat rounded-xl px-5 py-4 flex flex-col sm:flex-row items-center justify-between gap-4" style="background:linear-gradient(135deg,rgba(0,229,255,0.08),rgba(255,62,141,0.08))">
      <div class="flex items-center gap-3">
        <i class="fab fa-telegram text-3xl text-primary"></i>
        <div>
          <p class="font-bold text-white">Join our Telegram Channel</p>
          <p class="text-xs text-white/50">Get notified about new movies and shows</p>
        </div>
      </div>
      <a href="<?= htmlspecialchars($siteTelegramLink, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener" class="btn-primary flex-shrink-0">
        <i class="fab fa-telegram"></i> Join Now
      </a>
    </div>
  </div>
</section>
<?php endif; ?>

<main class="max-w-7xl mx-auto px-4 pb-12 space-y-10">

  <!-- WATCHLIST ROW -->
  <?php if (!empty($watchlistItems)): ?>
  <section>
    <div class="flex items-center justify-between mb-3">
      <h2 class="section-title">My Watchlist</h2>
      <a href="watchlist.php" class="text-xs text-primary hover:underline">View All</a>
    </div>
    <div class="content-row">
      <?php foreach ($watchlistItems as $w): ?>
      <?php renderCard($w, $w['media_type'], (int)$w['tmdb_id'], $w['poster_path'], $w['title']); ?>
      <?php endforeach; ?>
    </div>
  </section>
  <?php endif; ?>

  <!-- WIDGETS OR DEFAULT ROWS -->
  <?php if (!empty($widgets)): ?>
    <?php foreach ($widgets as $widget): ?>
    <?php $wConfig = json_decode($widget['config_json'] ?? '{}', true) ?: []; ?>
    <?php if ($widget['type'] === 'content_row'): ?>
    <section>
      <h2 class="section-title"><?= htmlspecialchars($widget['title'] ?? '', ENT_QUOTES, 'UTF-8') ?></h2>
      <?php
      $rowItems = [];
      $source   = $wConfig['source'] ?? 'trending';
      if ($source === 'trending') {
          $rowItems = array_slice(!empty($trendingAll) ? $trendingAll : $dbMovies, 0, 20);
      } elseif ($source === 'movies') {
          $rowItems = array_slice(!empty($trendingMovies) ? $trendingMovies : array_filter($dbMovies, fn($x) => $x['media_type'] === 'movie'), 0, 20);
      } elseif ($source === 'tv') {
          $rowItems = array_slice(!empty($trendingTV) ? $trendingTV : array_filter($dbMovies, fn($x) => $x['media_type'] === 'tv'), 0, 20);
      } else {
          $rowItems = array_slice($dbMovies, 0, 20);
      }
      ?>
      <div class="content-row">
        <?php foreach ($rowItems as $item): ?>
        <?php
        $tmdbIdR  = isset($item['tmdb_id']) ? (int)$item['tmdb_id'] : (int)($item['id'] ?? 0);
        $typeR    = $item['media_type'] ?? 'movie';
        $titleR   = $item['title'] ?? $item['name'] ?? 'Untitled';
        $posterR  = $item['poster_path'] ?? null;
        $ratingR  = (float)($item['vote_average'] ?? 0);
        renderCard($item, $typeR, $tmdbIdR, $posterR, $titleR, $ratingR);
        ?>
        <?php endforeach; ?>
      </div>
    </section>

    <?php elseif ($widget['type'] === 'cta_banner'): ?>
    <section>
      <div class="neu-flat rounded-xl px-6 py-5 flex flex-col sm:flex-row items-center justify-between gap-4"
           style="background:linear-gradient(135deg,rgba(0,229,255,0.08),rgba(255,62,141,0.08))">
        <div>
          <h3 class="font-bold text-lg text-white"><?= htmlspecialchars($widget['title'] ?? '', ENT_QUOTES, 'UTF-8') ?></h3>
          <?php if (!empty($wConfig['subtitle'])): ?>
          <p class="text-sm text-white/50"><?= htmlspecialchars($wConfig['subtitle'], ENT_QUOTES, 'UTF-8') ?></p>
          <?php endif; ?>
        </div>
        <?php if (!empty($wConfig['button_text']) && !empty($wConfig['button_url'])): ?>
        <a href="<?= htmlspecialchars($wConfig['button_url'], ENT_QUOTES, 'UTF-8') ?>" class="btn-primary flex-shrink-0">
          <?= htmlspecialchars($wConfig['button_text'], ENT_QUOTES, 'UTF-8') ?>
        </a>
        <?php endif; ?>
      </div>
    </section>

    <?php elseif ($widget['type'] === 'menu_links'): ?>
    <section>
      <?php if ($widget['title']): ?><h2 class="section-title mb-2"><?= htmlspecialchars($widget['title'], ENT_QUOTES, 'UTF-8') ?></h2><?php endif; ?>
      <div class="flex flex-wrap gap-2">
        <?php foreach ($wConfig['links'] ?? [] as $link): ?>
        <a href="<?= htmlspecialchars($link['url'] ?? '#', ENT_QUOTES, 'UTF-8') ?>" class="btn-outline text-sm">
          <?= htmlspecialchars($link['label'] ?? 'Link', ENT_QUOTES, 'UTF-8') ?>
        </a>
        <?php endforeach; ?>
      </div>
    </section>
    <?php endif; ?>
    <?php endforeach; ?>

  <?php else: ?>
    <!-- DEFAULT ROWS when no widgets configured -->

    <!-- Trending Movies -->
    <?php
    $tMovies = !empty($trendingMovies) ? array_slice($trendingMovies, 0, 20) : array_slice(array_filter($dbMovies, fn($m) => $m['media_type'] === 'movie'), 0, 20);
    if (!empty($tMovies)):
    ?>
    <section>
      <div class="flex items-center justify-between mb-1">
        <h2 class="section-title">Trending Movies</h2>
        <a href="search.php?type=movie" class="text-xs text-primary hover:underline">See All</a>
      </div>
      <div class="content-row">
        <?php foreach ($tMovies as $m): ?>
        <?php
        $tmdbIdM = isset($m['tmdb_id']) ? (int)$m['tmdb_id'] : (int)($m['id'] ?? 0);
        $typeM   = 'movie';
        $titleM  = $m['title'] ?? 'Untitled';
        $posterM = $m['poster_path'] ?? null;
        $ratingM = (float)($m['vote_average'] ?? 0);
        renderCard($m, $typeM, $tmdbIdM, $posterM, $titleM, $ratingM);
        ?>
        <?php endforeach; ?>
      </div>
    </section>
    <?php endif; ?>

    <!-- Popular TV Shows -->
    <?php
    $tTVS = !empty($trendingTV) ? array_slice($trendingTV, 0, 20) : array_slice(array_filter($dbMovies, fn($m) => $m['media_type'] === 'tv'), 0, 20);
    if (!empty($tTVS)):
    ?>
    <section>
      <div class="flex items-center justify-between mb-1">
        <h2 class="section-title">Popular TV Shows</h2>
        <a href="search.php?type=tv" class="text-xs text-primary hover:underline">See All</a>
      </div>
      <div class="content-row">
        <?php foreach ($tTVS as $m): ?>
        <?php
        $tmdbIdT = isset($m['tmdb_id']) ? (int)$m['tmdb_id'] : (int)($m['id'] ?? 0);
        $typeT   = 'tv';
        $titleT  = $m['name'] ?? $m['title'] ?? 'Untitled';
        $posterT = $m['poster_path'] ?? null;
        $ratingT = (float)($m['vote_average'] ?? 0);
        renderCard($m, $typeT, $tmdbIdT, $posterT, $titleT, $ratingT);
        ?>
        <?php endforeach; ?>
      </div>
    </section>
    <?php endif; ?>

    <!-- Top Picks (DB movies) -->
    <?php if (!empty($dbMovies)): ?>
    <section>
      <div class="flex items-center justify-between mb-1">
        <h2 class="section-title">Top Picks</h2>
        <a href="search.php" class="text-xs text-primary hover:underline">Browse All</a>
      </div>
      <div class="content-row">
        <?php foreach (array_slice($dbMovies, 0, 20) as $m): ?>
        <?php renderCard($m, $m['media_type'], (int)$m['tmdb_id'], $m['poster_path'], $m['title'], (float)($m['vote_average'] ?? 0)); ?>
        <?php endforeach; ?>
      </div>
    </section>
    <?php endif; ?>
  <?php endif; ?>

  <!-- BROWSE LIBRARY -->
  <?php if (!empty($dbMovies)): ?>
  <section>
    <h2 class="section-title mb-4">Browse Library</h2>
    <!-- Filters -->
    <div class="flex flex-wrap items-center gap-2 mb-4">
      <button onclick="filterLibrary('all','genre',this)" class="btn-primary text-xs py-1.5 px-3 rounded-lg active-filter">All</button>
      <?php
      $genres = [];
      foreach ($dbMovies as $m) {
          $gs = json_decode($m['genres_json'] ?? '[]', true) ?: [];
          foreach ($gs as $g) {
              if (!in_array($g, $genres) && count($genres) < 10) $genres[] = $g;
          }
      }
      foreach ($genres as $g):
      ?>
      <button onclick="filterLibrary('<?= htmlspecialchars($g, ENT_QUOTES, 'UTF-8') ?>','genre',this)" class="btn-outline text-xs py-1.5 px-3 rounded-lg"><?= htmlspecialchars($g, ENT_QUOTES, 'UTF-8') ?></button>
      <?php endforeach; ?>
      <span class="mx-1 text-white/20">|</span>
      <button onclick="filterLibrary('all','type',this)" class="btn-outline text-xs py-1.5 px-3 rounded-lg type-filter active-type">All Types</button>
      <button onclick="filterLibrary('movie','type',this)" class="btn-outline text-xs py-1.5 px-3 rounded-lg type-filter">Movies</button>
      <button onclick="filterLibrary('tv','type',this)" class="btn-outline text-xs py-1.5 px-3 rounded-lg type-filter">TV Shows</button>
    </div>
    <div id="libraryGrid" class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-7 gap-3">
      <?php foreach (array_slice($dbMovies, 0, 42) as $m): ?>
      <?php
      $gs    = json_decode($m['genres_json'] ?? '[]', true) ?: [];
      $gAttr = implode('|', array_map(fn($x) => htmlspecialchars($x, ENT_QUOTES, 'UTF-8'), $gs));
      ?>
      <div class="lib-item" data-type="<?= htmlspecialchars($m['media_type'], ENT_QUOTES, 'UTF-8') ?>" data-genres="<?= $gAttr ?>">
        <a href="watch.php?type=<?= htmlspecialchars($m['media_type'], ENT_QUOTES, 'UTF-8') ?>&id=<?= (int)$m['tmdb_id'] ?>" class="movie-card block">
          <img src="<?= htmlspecialchars(posterUrl($m['poster_path']), ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($m['title'], ENT_QUOTES, 'UTF-8') ?>" loading="lazy">
          <div class="overlay"></div>
          <div class="play-btn"><i class="fas fa-play text-sm"></i></div>
          <?php if ($m['vote_average'] > 0): ?><div class="rating"><i class="fas fa-star text-yellow-400 text-xs"></i><?= number_format((float)$m['vote_average'], 1) ?></div><?php endif; ?>
        </a>
        <p class="text-xs text-white/70 mt-1 truncate"><?= htmlspecialchars($m['title'], ENT_QUOTES, 'UTF-8') ?></p>
      </div>
      <?php endforeach; ?>
    </div>
  </section>
  <?php endif; ?>

</main>

<script>
var activeGenre = 'all', activeType = 'all';
function filterLibrary(val, cat, btn){
  if(cat==='genre'){ activeGenre = val; document.querySelectorAll('.active-filter').forEach(function(b){ b.className = b.className.replace('btn-primary','btn-outline'); b.classList.remove('active-filter'); }); btn.className = btn.className.replace('btn-outline','btn-primary'); btn.classList.add('active-filter'); }
  if(cat==='type'){ activeType = val; document.querySelectorAll('.type-filter').forEach(function(b){ b.classList.remove('border-primary','text-primary'); }); btn.classList.add('border-primary','text-primary'); }
  document.querySelectorAll('.lib-item').forEach(function(el){
    var t = el.dataset.type; var g = el.dataset.genres || '';
    var show = (activeGenre==='all' || g.toLowerCase().includes(activeGenre.toLowerCase())) && (activeType==='all' || t===activeType);
    el.style.display = show ? '' : 'none';
  });
}
</script>

<?php layout_footer($pdo); ?>
