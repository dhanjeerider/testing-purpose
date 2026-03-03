<?php
require __DIR__ . '/includes/db.php';
require __DIR__ . '/includes/helpers.php';
require __DIR__ . '/includes/tmdb.php';
require __DIR__ . '/includes/layout.php';

$config = require __DIR__ . '/config.php';

$query   = trim($_GET['q'] ?? '');
$type    = $_GET['type'] ?? 'all';   // all | movie | tv | anime
$sort    = $_GET['sort'] ?? 'relevance'; // relevance | date | rating
$page    = max(1, (int)($_GET['page'] ?? 1));
$perPage = 24;

// --- Results ---
$tmdbResults = [];
$dbResults   = [];
$totalDb     = 0;

if ($query !== '') {
    // TMDB search
    $tmdbType = $type === 'movie' ? 'movie' : ($type === 'tv' ? 'tv' : 'multi');
    $tmdbResults = searchTMDB($config, $query, $tmdbType);
    if ($type === 'anime') {
        $tmdbResults = array_filter($tmdbResults, fn($r) => in_array(16, $r['genre_ids'] ?? []));
    }
    // DB search
    $typeClause = '';
    $params = ['%' . $query . '%', '%' . $query . '%'];
    if ($type !== 'all' && $type !== 'anime') { $typeClause = ' AND media_type = ?'; $params[] = $type; }
    $orderBy = $sort === 'date' ? 'release_date DESC' : ($sort === 'rating' ? 'vote_average DESC' : 'vote_average DESC');
    $stmt = $pdo->prepare('SELECT * FROM movies WHERE is_active=1 AND (title LIKE ? OR overview LIKE ?) ' . $typeClause . ' ORDER BY ' . $orderBy . ' LIMIT 40');
    $stmt->execute($params);
    $dbResults = $stmt->fetchAll();
} else {
    // Paginated all movies
    $typeClause = '';
    $params = [];
    if ($type !== 'all' && $type !== 'anime') { $typeClause = ' AND media_type = ?'; $params[] = $type; }
    $countStmt = $pdo->prepare('SELECT COUNT(*) FROM movies WHERE is_active=1' . $typeClause);
    $countStmt->execute($params);
    $totalDb = (int)$countStmt->fetchColumn();

    $orderBy = $sort === 'date' ? 'release_date DESC' : ($sort === 'rating' ? 'vote_average DESC' : 'id DESC');
    $offset  = ($page - 1) * $perPage;
    $stmt    = $pdo->prepare('SELECT * FROM movies WHERE is_active=1' . $typeClause . ' ORDER BY ' . $orderBy . ' LIMIT ' . $perPage . ' OFFSET ' . $offset);
    $stmt->execute($params);
    $dbResults = $stmt->fetchAll();
}

$totalPages = $totalDb > 0 ? (int)ceil($totalDb / $perPage) : 1;

// Merge TMDB + DB for display (when query set, show TMDB results first, then DB-only)
$tmdbIds = [];
$allResults = [];
if (!empty($tmdbResults)) {
    foreach ($tmdbResults as $r) {
        $mt = $r['media_type'] ?? ($type === 'tv' ? 'tv' : 'movie');
        $tmdbIds[] = (int)$r['id'];
        $allResults[] = [
            'tmdb_id'    => (int)$r['id'],
            'media_type' => $mt,
            'title'      => $r['title'] ?? $r['name'] ?? 'Untitled',
            'poster_path'=> $r['poster_path'] ?? null,
            'vote_average'=> (float)($r['vote_average'] ?? 0),
            'release_date'=> $r['release_date'] ?? $r['first_air_date'] ?? '',
            '_source'    => 'tmdb',
        ];
    }
}
foreach ($dbResults as $d) {
    if (!in_array((int)$d['tmdb_id'], $tmdbIds)) {
        $d['_source'] = 'db';
        $allResults[] = $d;
    }
}
if (empty($tmdbResults)) $allResults = $dbResults;

// Sort merged list
if ($sort === 'rating') usort($allResults, fn($a, $b) => ($b['vote_average'] ?? 0) <=> ($a['vote_average'] ?? 0));
if ($sort === 'date') usort($allResults, fn($a, $b) => strcmp($b['release_date'] ?? '', $a['release_date'] ?? ''));

$pageTitle = $query ? 'Search: ' . $query : 'Search & Browse';
layout_head($pageTitle, $pdo);
layout_header($pdo);
?>

<main class="max-w-7xl mx-auto px-4 py-6">
  <!-- Search header -->
  <div class="mb-6">
    <h1 class="text-2xl font-black text-white mb-4">
      <?php if ($query): ?>
        Results for "<span class="text-primary"><?= htmlspecialchars($query, ENT_QUOTES, 'UTF-8') ?></span>"
      <?php else: ?>
        <i class="fas fa-search text-primary mr-2"></i>Browse Library
      <?php endif; ?>
    </h1>

    <!-- Search form -->
    <form method="GET" class="flex gap-2 mb-4">
      <div class="relative flex-1 max-w-xl">
        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-white/30 text-sm"></i>
        <input type="text" name="q" value="<?= htmlspecialchars($query, ENT_QUOTES, 'UTF-8') ?>"
               placeholder="Search movies, TV shows..."
               class="input-dark pl-9" autofocus>
      </div>
      <input type="hidden" name="type" value="<?= htmlspecialchars($type, ENT_QUOTES, 'UTF-8') ?>">
      <input type="hidden" name="sort" value="<?= htmlspecialchars($sort, ENT_QUOTES, 'UTF-8') ?>">
      <button type="submit" class="btn-primary"><i class="fas fa-search"></i><span class="hidden sm:inline"> Search</span></button>
    </form>

    <!-- Filter bar -->
    <div class="flex items-center gap-2 flex-wrap">
      <!-- Type filters -->
      <?php
      $types = ['all'=>'All','movie'=>'Movies','tv'=>'TV Shows','anime'=>'Anime'];
      foreach ($types as $k => $label):
      $active = $type === $k;
      ?>
      <a href="?q=<?= urlencode($query) ?>&type=<?= $k ?>&sort=<?= urlencode($sort) ?>"
         class="text-xs font-semibold px-3 py-1.5 rounded-lg border transition-all <?= $active ? 'border-primary text-primary bg-primary/10' : 'border-white/10 text-white/50 hover:border-white/30 hover:text-white' ?>">
        <?= $label ?>
      </a>
      <?php endforeach; ?>

      <span class="flex-1"></span>

      <!-- Sort dropdown -->
      <div class="flex items-center gap-2">
        <span class="text-xs text-white/40">Sort:</span>
        <select onchange="location.href=this.value" class="input-dark text-xs py-1.5 px-2 w-auto">
          <?php
          $sorts = ['relevance'=>'Relevance','date'=>'Newest','rating'=>'Top Rated'];
          foreach ($sorts as $k => $label):
          $qs = http_build_query(['q'=>$query,'type'=>$type,'sort'=>$k,'page'=>1]);
          ?>
          <option value="?<?= $qs ?>" <?= $sort === $k ? 'selected' : '' ?>><?= $label ?></option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>
  </div>

  <!-- Result count -->
  <?php if ($query): ?>
  <p class="text-sm text-white/40 mb-4">
    <?= count($allResults) ?> results
    <?php if (!empty($tmdbResults)): ?>
    <span class="ml-2 text-xs text-white/25">(including TMDB)</span>
    <?php endif; ?>
  </p>
  <?php else: ?>
  <p class="text-sm text-white/40 mb-4"><?= number_format($totalDb) ?> titles in library</p>
  <?php endif; ?>

  <!-- Results grid -->
  <?php if (!empty($allResults)): ?>
  <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-6 xl:grid-cols-8 gap-3 mb-8">
    <?php foreach ($allResults as $item): ?>
    <?php
    $tmdbIdI  = (int)($item['tmdb_id'] ?? $item['id'] ?? 0);
    $typeI    = $item['media_type'] ?? 'movie';
    $titleI   = $item['title'] ?? $item['name'] ?? 'Untitled';
    $posterI  = $item['poster_path'] ?? null;
    $ratingI  = (float)($item['vote_average'] ?? 0);
    $yearI    = substr($item['release_date'] ?? '', 0, 4);
    $url      = 'watch.php?type=' . htmlspecialchars($typeI, ENT_QUOTES, 'UTF-8') . '&id=' . $tmdbIdI;
    ?>
    <div>
      <a href="<?= $url ?>" class="movie-card block">
        <img src="<?= htmlspecialchars(posterUrl($posterI), ENT_QUOTES, 'UTF-8') ?>"
             alt="<?= htmlspecialchars($titleI, ENT_QUOTES, 'UTF-8') ?>" loading="lazy">
        <div class="overlay"></div>
        <div class="play-btn"><i class="fas fa-play text-sm"></i></div>
        <?php if ($ratingI > 0): ?><div class="rating"><i class="fas fa-star text-yellow-400 text-xs"></i><?= number_format($ratingI, 1) ?></div><?php endif; ?>
        <div class="badge-type"><?= $typeI === 'tv' ? 'TV' : 'Movie' ?></div>
      </a>
      <p class="text-xs text-white/70 mt-1 truncate"><?= htmlspecialchars($titleI, ENT_QUOTES, 'UTF-8') ?></p>
      <?php if ($yearI): ?><p class="text-xs text-white/30"><?= $yearI ?></p><?php endif; ?>
    </div>
    <?php endforeach; ?>
  </div>
  <?php elseif ($query): ?>
  <div class="text-center py-20">
    <i class="fas fa-search text-5xl text-white/10 mb-4"></i>
    <p class="text-white/40 text-lg">No results found for "<?= htmlspecialchars($query, ENT_QUOTES, 'UTF-8') ?>"</p>
    <p class="text-white/25 text-sm mt-1">Try different keywords or browse our library</p>
    <a href="search.php" class="btn-outline mt-4 inline-flex">Browse All</a>
  </div>
  <?php else: ?>
  <div class="text-center py-20">
    <i class="fas fa-film text-5xl text-white/10 mb-4"></i>
    <p class="text-white/40">No movies in library yet.</p>
    <?php if (isLoggedIn()): ?><a href="admin.php?tab=add-post" class="btn-primary mt-4 inline-flex">Add Movies</a><?php endif; ?>
  </div>
  <?php endif; ?>

  <!-- Pagination (only for no-query browse) -->
  <?php if (!$query && $totalPages > 1): ?>
  <div class="flex items-center justify-center gap-2 flex-wrap">
    <?php if ($page > 1): ?>
    <a href="?<?= http_build_query(['q'=>'','type'=>$type,'sort'=>$sort,'page'=>$page-1]) ?>" class="btn-outline text-sm py-2 px-4">
      <i class="fas fa-chevron-left"></i>
    </a>
    <?php endif; ?>
    <?php for ($p = max(1,$page-2); $p <= min($totalPages, $page+2); $p++): ?>
    <a href="?<?= http_build_query(['q'=>'','type'=>$type,'sort'=>$sort,'page'=>$p]) ?>"
       class="text-sm py-2 px-4 rounded-lg font-medium transition-all <?= $p === $page ? 'bg-primary/20 text-primary border border-primary/40' : 'text-white/50 hover:text-white neu-flat' ?>">
      <?= $p ?>
    </a>
    <?php endfor; ?>
    <?php if ($page < $totalPages): ?>
    <a href="?<?= http_build_query(['q'=>'','type'=>$type,'sort'=>$sort,'page'=>$page+1]) ?>" class="btn-outline text-sm py-2 px-4">
      <i class="fas fa-chevron-right"></i>
    </a>
    <?php endif; ?>
  </div>
  <?php endif; ?>
</main>

<?php layout_footer($pdo); ?>
