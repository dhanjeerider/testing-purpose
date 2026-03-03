<?php
require __DIR__ . '/includes/db.php';
require __DIR__ . '/includes/helpers.php';
require __DIR__ . '/includes/tmdb.php';
require __DIR__ . '/includes/layout.php';

requireLogin();
$config = require __DIR__ . '/config.php';

$tab = $_GET['tab'] ?? 'movies';
$msg = $_GET['msg'] ?? '';

// Stats
$statsMovies  = (int)$pdo->query('SELECT COUNT(*) FROM movies WHERE is_active=1')->fetchColumn();
$statsStream  = (int)$pdo->query("SELECT COUNT(*) FROM custom_servers WHERE is_active=1 AND is_download=0")->fetchColumn();
$statsDl      = (int)$pdo->query("SELECT COUNT(*) FROM custom_servers WHERE is_active=1 AND is_download=1")->fetchColumn();
$statsWidgets = (int)$pdo->query('SELECT COUNT(*) FROM widgets WHERE is_active=1')->fetchColumn();
$statsWl      = (int)$pdo->query('SELECT COUNT(*) FROM watchlist')->fetchColumn();
$statsPages   = (int)$pdo->query('SELECT COUNT(*) FROM pages WHERE is_active=1')->fetchColumn();

// TMDB search (GET-based, stays on same page)
$tmdbSearchQuery   = trim($_GET['tmdb_q'] ?? '');
$tmdbSearchResults = [];
$tmdbSearchType    = $_GET['tmdb_type'] ?? 'multi';
if ($tmdbSearchQuery) {
    $tmdbSearchResults = searchTMDB($config, $tmdbSearchQuery, $tmdbSearchType);
}

// Movies list
$movieSearch = trim($_GET['ms'] ?? '');
$movieType   = $_GET['mt'] ?? 'all';
$movieSort   = $_GET['msort'] ?? 'id';
$moviePage   = max(1, (int)($_GET['mp'] ?? 1));
$mPerPage    = 20;

$mWhere  = 'WHERE 1';
$mParams = [];
if ($movieSearch) { $mWhere .= ' AND (title LIKE ? OR overview LIKE ?)'; $mParams[] = "%$movieSearch%"; $mParams[] = "%$movieSearch%"; }
if ($movieType !== 'all') { $mWhere .= ' AND media_type = ?'; $mParams[] = $movieType; }
$mOrder  = in_array($movieSort, ['id','title','vote_average','release_date']) ? $movieSort : 'id';
$mCountStmt = $pdo->prepare("SELECT COUNT(*) FROM movies $mWhere");
$mCountStmt->execute($mParams);
$mTotal   = (int)$mCountStmt->fetchColumn();
$mOffset  = ($moviePage - 1) * $mPerPage;
$mStmt    = $pdo->prepare("SELECT * FROM movies $mWhere ORDER BY $mOrder DESC LIMIT $mPerPage OFFSET $mOffset");
$mStmt->execute($mParams);
$movies   = $mStmt->fetchAll();
$mTotalPages = max(1, (int)ceil($mTotal / $mPerPage));

// Edit movie
$editMovieId = (int)($_GET['edit_movie'] ?? 0);
$editMovie   = null;
if ($editMovieId) {
    $stmt = $pdo->prepare('SELECT * FROM movies WHERE id=?');
    $stmt->execute([$editMovieId]);
    $editMovie = $stmt->fetch();
}

// Servers
$servers   = $pdo->query('SELECT * FROM custom_servers ORDER BY is_default DESC, sort_order ASC, id ASC')->fetchAll();
$editServerId = (int)($_GET['edit_server'] ?? 0);
$editServer  = null;
if ($editServerId) {
    $stmt2 = $pdo->prepare('SELECT * FROM custom_servers WHERE id=?');
    $stmt2->execute([$editServerId]);
    $editServer = $stmt2->fetch();
}

// Widgets
$widgets = $pdo->query('SELECT * FROM widgets ORDER BY sort_order ASC, id ASC')->fetchAll();

// Pages
$pages = $pdo->query('SELECT * FROM pages ORDER BY sort_order ASC, id ASC')->fetchAll();
$editPageId = (int)($_GET['edit_page'] ?? 0);
$editPage   = null;
if ($editPageId) {
    $stmt3 = $pdo->prepare('SELECT * FROM pages WHERE id=?');
    $stmt3->execute([$editPageId]);
    $editPage = $stmt3->fetch();
}

// UPI payments
$payments = $pdo->query('SELECT * FROM upi_payments ORDER BY id DESC LIMIT 50')->fetchAll();

// Settings
function s(PDO $pdo, string $key): string { return setting($pdo, $key, ''); }

layout_head('Admin Dashboard', $pdo);
layout_header($pdo);

// Message map
$msgs = [
    'login-success'    => ['success','Logged in successfully.'],
    'imported'         => ['success','Movie imported from TMDB.'],
    'movie-added'      => ['success','Movie added successfully.'],
    'deleted'          => ['success','Movie deleted.'],
    'server-saved'     => ['success','Server saved.'],
    'server-deleted'   => ['success','Server deleted.'],
    'widget-saved'     => ['success','Widget saved.'],
    'widget-deleted'   => ['success','Widget deleted.'],
    'page-saved'       => ['success','Page saved.'],
    'page-deleted'     => ['success','Page deleted.'],
    'payment-updated'  => ['success','Payment status updated.'],
    'settings-updated' => ['success','Settings saved.'],
    'saved'            => ['success','Setting saved.'],
    'comment-deleted'  => ['success','Comment deleted.'],
];
?>

<main class="max-w-7xl mx-auto px-4 py-6 pb-12">

  <!-- Page header -->
  <div class="flex items-center justify-between mb-6">
    <div>
      <h1 class="text-2xl font-black text-white flex items-center gap-2">
        <i class="fas fa-cog text-primary"></i> Admin Dashboard
      </h1>
      <p class="text-sm text-white/40 mt-0.5">Welcome, <?= htmlspecialchars($_SESSION['admin_username'] ?? 'Admin', ENT_QUOTES, 'UTF-8') ?></p>
    </div>
    <form method="post" action="actions.php">
      <input type="hidden" name="action" value="logout">
      <button type="submit" class="btn-outline text-sm py-2 px-4">
        <i class="fas fa-sign-out-alt"></i> Logout
      </button>
    </form>
  </div>

  <!-- Alert -->
  <?php if ($msg && isset($msgs[$msg])): ?>
  <div class="alert alert-<?= $msgs[$msg][0] ?> mb-4">
    <i class="fas fa-check-circle mr-2"></i><?= htmlspecialchars($msgs[$msg][1], ENT_QUOTES, 'UTF-8') ?>
  </div>
  <?php endif; ?>

  <!-- Stats Row -->
  <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 mb-6">
    <?php
    $stats = [
      ['Movies','fas fa-film text-primary',$statsMovies],
      ['Streaming','fas fa-play-circle text-green-400',$statsStream],
      ['Download','fas fa-download text-blue-400',$statsDl],
      ['Widgets','fas fa-th-large text-yellow-400',$statsWidgets],
      ['Watchlist','fas fa-bookmark text-accent',$statsWl],
      ['Pages','fas fa-file-alt text-purple-400',$statsPages],
    ];
    foreach ($stats as [$label,$icon,$val]):
    ?>
    <div class="neu-flat rounded-xl p-4 text-center">
      <i class="<?= $icon ?> text-2xl mb-1"></i>
      <p class="text-xl font-black text-white"><?= $val ?></p>
      <p class="text-xs text-white/40"><?= $label ?></p>
    </div>
    <?php endforeach; ?>
  </div>

  <!-- Tabs -->
  <div class="border-b border-white/10 mb-6 overflow-x-auto" style="scrollbar-width:none">
    <div class="flex" style="min-width:max-content">
      <?php
      $tabs = ['movies'=>'Movies & TV','add-post'=>'Add Post','widgets'=>'Widgets','servers'=>'Servers','pages'=>'Pages','upi'=>'UPI Payments','settings'=>'Settings'];
      foreach ($tabs as $k => $label):
      ?>
      <a href="?tab=<?= $k ?>" class="tab-btn <?= $tab === $k ? 'active' : '' ?>"><?= $label ?></a>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- ==================== MOVIES TAB ==================== -->
  <?php if ($tab === 'movies'): ?>
  <div class="space-y-5">
    <!-- Filter bar -->
    <form method="GET" class="flex flex-wrap gap-2">
      <input type="hidden" name="tab" value="movies">
      <input type="text" name="ms" value="<?= htmlspecialchars($movieSearch, ENT_QUOTES, 'UTF-8') ?>" placeholder="Search title..." class="input-dark text-sm flex-1 min-w-48 max-w-xs">
      <select name="mt" class="input-dark text-sm w-auto">
        <option value="all" <?= $movieType==='all'?'selected':'' ?>>All Types</option>
        <option value="movie" <?= $movieType==='movie'?'selected':'' ?>>Movies</option>
        <option value="tv" <?= $movieType==='tv'?'selected':'' ?>>TV Shows</option>
      </select>
      <select name="msort" class="input-dark text-sm w-auto">
        <option value="id" <?= $movieSort==='id'?'selected':'' ?>>Newest</option>
        <option value="title" <?= $movieSort==='title'?'selected':'' ?>>Title</option>
        <option value="vote_average" <?= $movieSort==='vote_average'?'selected':'' ?>>Rating</option>
        <option value="release_date" <?= $movieSort==='release_date'?'selected':'' ?>>Date</option>
      </select>
      <button type="submit" class="btn-primary text-sm py-2 px-4"><i class="fas fa-search"></i></button>
      <a href="?tab=movies" class="btn-outline text-sm py-2 px-3"><i class="fas fa-times"></i></a>
    </form>

    <!-- Edit Movie Form (inline) -->
    <?php if ($editMovie): ?>
    <div class="neu-raised rounded-xl p-5 border border-primary/20">
      <h3 class="text-base font-bold text-white mb-4 flex items-center gap-2">
        <i class="fas fa-edit text-primary"></i> Edit: <?= htmlspecialchars($editMovie['title'], ENT_QUOTES, 'UTF-8') ?>
        <a href="?tab=movies" class="ml-auto text-sm text-white/40 hover:text-white"><i class="fas fa-times"></i></a>
      </h3>
      <form method="post" action="actions.php">
        <input type="hidden" name="action" value="save_movie">
        <input type="hidden" name="tmdb_id" value="<?= (int)$editMovie['tmdb_id'] ?>">
        <input type="hidden" name="media_type" value="<?= htmlspecialchars($editMovie['media_type'], ENT_QUOTES, 'UTF-8') ?>">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div><label class="text-xs text-white/50 block mb-1">Title</label><input type="text" name="title" value="<?= htmlspecialchars($editMovie['title'], ENT_QUOTES, 'UTF-8') ?>" required class="input-dark text-sm"></div>
          <div><label class="text-xs text-white/50 block mb-1">Release Date</label><input type="text" name="release_date" value="<?= htmlspecialchars($editMovie['release_date'] ?? '', ENT_QUOTES, 'UTF-8') ?>" class="input-dark text-sm"></div>
          <div><label class="text-xs text-white/50 block mb-1">Vote Average</label><input type="number" step="0.1" name="vote_average" value="<?= htmlspecialchars($editMovie['vote_average'] ?? '0', ENT_QUOTES, 'UTF-8') ?>" class="input-dark text-sm"></div>
          <div><label class="text-xs text-white/50 block mb-1">Poster Path</label><input type="text" name="poster_path" value="<?= htmlspecialchars($editMovie['poster_path'] ?? '', ENT_QUOTES, 'UTF-8') ?>" class="input-dark text-sm"></div>
          <div><label class="text-xs text-white/50 block mb-1">Backdrop Path</label><input type="text" name="backdrop_path" value="<?= htmlspecialchars($editMovie['backdrop_path'] ?? '', ENT_QUOTES, 'UTF-8') ?>" class="input-dark text-sm"></div>
          <div class="flex items-center gap-4 pt-4">
            <label class="flex items-center gap-2 text-sm text-white/60 cursor-pointer">
              <input type="checkbox" name="is_featured" value="1" <?= $editMovie['is_featured'] ? 'checked' : '' ?> class="rounded">Featured
            </label>
          </div>
          <div class="sm:col-span-2"><label class="text-xs text-white/50 block mb-1">Overview</label><textarea name="overview" rows="3" class="input-dark text-sm resize-none"><?= htmlspecialchars($editMovie['overview'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea></div>
          <div class="sm:col-span-2"><label class="text-xs text-white/50 block mb-1">Download Links JSON</label><textarea name="download_links_json" rows="2" class="input-dark text-sm resize-none font-mono"><?= htmlspecialchars($editMovie['download_links_json'] ?? '[]', ENT_QUOTES, 'UTF-8') ?></textarea></div>
        </div>
        <div class="flex gap-2 mt-4">
          <button type="submit" class="btn-primary text-sm py-2 px-5"><i class="fas fa-save"></i> Save</button>
          <a href="?tab=movies" class="btn-outline text-sm py-2 px-4">Cancel</a>
        </div>
      </form>
    </div>
    <?php endif; ?>

    <!-- TMDB Import Search -->
    <div class="neu-flat rounded-xl p-5">
      <h3 class="text-sm font-bold text-white mb-3 flex items-center gap-2">
        <i class="fas fa-cloud-download-alt text-primary"></i> Import from TMDB
      </h3>
      <form method="GET" class="flex gap-2 mb-4">
        <input type="hidden" name="tab" value="movies">
        <input type="text" name="tmdb_q" value="<?= htmlspecialchars($tmdbSearchQuery, ENT_QUOTES, 'UTF-8') ?>" placeholder="Search TMDB..." class="input-dark text-sm flex-1">
        <select name="tmdb_type" class="input-dark text-sm w-auto">
          <option value="multi" <?= $tmdbSearchType==='multi'?'selected':'' ?>>All</option>
          <option value="movie" <?= $tmdbSearchType==='movie'?'selected':'' ?>>Movie</option>
          <option value="tv" <?= $tmdbSearchType==='tv'?'selected':'' ?>>TV Show</option>
        </select>
        <button type="submit" class="btn-primary text-sm py-2 px-4"><i class="fas fa-search"></i></button>
      </form>
      <?php if ($tmdbSearchQuery && empty($tmdbSearchResults)): ?>
      <p class="text-sm text-white/40">No TMDB results found.</p>
      <?php elseif (!empty($tmdbSearchResults)): ?>
      <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3">
        <?php foreach (array_slice($tmdbSearchResults, 0, 15) as $r): ?>
        <?php
        $rId    = (int)$r['id'];
        $rType  = $r['media_type'] ?? ($tmdbSearchType === 'movie' ? 'movie' : 'tv');
        $rTitle = $r['title'] ?? $r['name'] ?? 'Untitled';
        $rYear  = substr($r['release_date'] ?? $r['first_air_date'] ?? '', 0, 4);
        $rRating= (float)($r['vote_average'] ?? 0);
        ?>
        <div class="text-center">
          <div class="movie-card mb-1 group relative" style="aspect-ratio:2/3">
            <img src="<?= htmlspecialchars(posterUrl($r['poster_path'] ?? null), ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($rTitle, ENT_QUOTES, 'UTF-8') ?>" loading="lazy" class="w-full h-full object-cover">
            <div class="overlay flex items-end justify-center pb-2 opacity-0 group-hover:opacity-100 transition-opacity">
              <form method="post" action="actions.php">
                <input type="hidden" name="action" value="import_tmdb">
                <input type="hidden" name="tmdb_id" value="<?= $rId ?>">
                <input type="hidden" name="media_type" value="<?= htmlspecialchars($rType, ENT_QUOTES, 'UTF-8') ?>">
                <input type="hidden" name="title" value="<?= htmlspecialchars($rTitle, ENT_QUOTES, 'UTF-8') ?>">
                <input type="hidden" name="overview" value="<?= htmlspecialchars($r['overview'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                <input type="hidden" name="poster_path" value="<?= htmlspecialchars($r['poster_path'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                <input type="hidden" name="backdrop_path" value="<?= htmlspecialchars($r['backdrop_path'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                <input type="hidden" name="release_date" value="<?= htmlspecialchars($r['release_date'] ?? $r['first_air_date'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                <input type="hidden" name="vote_average" value="<?= $rRating ?>">
                <button type="submit" class="btn-primary text-xs py-1 px-3">
                  <i class="fas fa-plus"></i> Import
                </button>
              </form>
            </div>
            <?php if ($rRating > 0): ?><div class="rating"><i class="fas fa-star text-yellow-400 text-xs"></i><?= number_format($rRating, 1) ?></div><?php endif; ?>
          </div>
          <p class="text-xs text-white/70 truncate"><?= htmlspecialchars($rTitle, ENT_QUOTES, 'UTF-8') ?></p>
          <?php if ($rYear): ?><p class="text-xs text-white/30"><?= $rYear ?></p><?php endif; ?>
        </div>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>

      <!-- Bulk import trending -->
      <div class="mt-4 pt-4 border-t border-white/5">
        <p class="text-xs text-white/40 mb-2">Or bulk import trending from TMDB:</p>
        <?php
        $trendTypes = [['all','All Trending'],['movie','Trending Movies'],['tv','Trending TV']];
        foreach ($trendTypes as [$tt, $tl]):
        $trending = fetchTrending($config, $tt);
        if (!empty($trending)):
        ?>
        <form method="post" action="actions.php" class="inline-block mr-2 mb-2">
          <input type="hidden" name="action" value="bulk_import_start">
          <?php foreach (array_slice($trending, 0, 10) as $bt): ?>
          <?php
          $btType = $bt['media_type'] ?? ($tt === 'movie' ? 'movie' : 'tv');
          $btTitle = $bt['title'] ?? $bt['name'] ?? 'Untitled';
          ?>
          <!-- We do individual imports via JS approach; just show a summary button -->
          <?php endforeach; ?>
        </form>
        <?php // Instead just show single import buttons for each trending item in a separate area ?>
        <?php endif; endforeach; ?>
        <p class="text-xs text-white/25">Use the TMDB search above to find and import individual titles.</p>
      </div>
    </div>

    <!-- Movies table -->
    <div class="neu-flat rounded-xl overflow-hidden">
      <div class="flex items-center justify-between px-4 py-3 border-b border-white/5">
        <span class="text-sm font-semibold text-white"><?= $mTotal ?> Movies & Shows</span>
      </div>
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead>
            <tr class="border-b border-white/5 text-xs text-white/40 uppercase tracking-wider">
              <th class="text-left px-4 py-3">Title</th>
              <th class="text-center px-3 py-3">Type</th>
              <th class="text-center px-3 py-3">Rating</th>
              <th class="text-center px-3 py-3">Year</th>
              <th class="text-center px-3 py-3">Featured</th>
              <th class="text-center px-3 py-3">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($movies as $m): ?>
            <tr class="border-b border-white/5 hover:bg-white/2 transition-colors">
              <td class="px-4 py-3">
                <div class="flex items-center gap-3">
                  <img src="<?= htmlspecialchars(posterUrl($m['poster_path']), ENT_QUOTES, 'UTF-8') ?>" class="w-8 rounded flex-shrink-0" style="aspect-ratio:2/3;object-fit:cover" alt="" loading="lazy">
                  <div class="min-w-0">
                    <p class="font-medium text-white truncate max-w-xs"><?= htmlspecialchars($m['title'], ENT_QUOTES, 'UTF-8') ?></p>
                    <p class="text-xs text-white/30">ID: <?= $m['tmdb_id'] ?></p>
                  </div>
                </div>
              </td>
              <td class="px-3 py-3 text-center"><span class="text-xs px-2 py-0.5 rounded border border-white/10 text-primary"><?= strtoupper($m['media_type']) ?></span></td>
              <td class="px-3 py-3 text-center text-white/60"><?= number_format((float)$m['vote_average'], 1) ?></td>
              <td class="px-3 py-3 text-center text-white/40"><?= substr($m['release_date'] ?? '', 0, 4) ?></td>
              <td class="px-3 py-3 text-center"><?= $m['is_featured'] ? '<i class="fas fa-star text-yellow-400"></i>' : '<i class="far fa-star text-white/20"></i>' ?></td>
              <td class="px-3 py-3 text-center">
                <div class="flex items-center justify-center gap-2">
                  <a href="watch.php?type=<?= htmlspecialchars($m['media_type'], ENT_QUOTES, 'UTF-8') ?>&id=<?= (int)$m['tmdb_id'] ?>" target="_blank" class="text-white/40 hover:text-primary transition-colors text-xs" title="View"><i class="fas fa-eye"></i></a>
                  <a href="?tab=movies&edit_movie=<?= (int)$m['id'] ?>" class="text-white/40 hover:text-primary transition-colors text-xs" title="Edit"><i class="fas fa-edit"></i></a>
                  <form method="post" action="actions.php" class="inline" onsubmit="return confirm('Delete this?')">
                    <input type="hidden" name="action" value="delete_movie">
                    <input type="hidden" name="id" value="<?= (int)$m['id'] ?>">
                    <button type="submit" class="text-white/40 hover:text-red-400 transition-colors text-xs" title="Delete"><i class="fas fa-trash"></i></button>
                  </form>
                </div>
              </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($movies)): ?>
            <tr><td colspan="6" class="px-4 py-8 text-center text-white/30">No movies found.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
      <!-- Pagination -->
      <?php if ($mTotalPages > 1): ?>
      <div class="flex items-center justify-center gap-2 p-4 border-t border-white/5">
        <?php for ($p = max(1, $moviePage - 2); $p <= min($mTotalPages, $moviePage + 2); $p++): ?>
        <a href="?tab=movies&ms=<?= urlencode($movieSearch) ?>&mt=<?= $movieType ?>&msort=<?= $movieSort ?>&mp=<?= $p ?>"
           class="text-sm py-1.5 px-3 rounded-lg <?= $p === $moviePage ? 'bg-primary/20 text-primary border border-primary/40' : 'neu-flat text-white/50 hover:text-white' ?>">
          <?= $p ?>
        </a>
        <?php endfor; ?>
      </div>
      <?php endif; ?>
    </div>
  </div>

  <!-- ==================== ADD POST TAB ==================== -->
  <?php elseif ($tab === 'add-post'): ?>
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Manual add -->
    <div class="neu-flat rounded-xl p-5">
      <h3 class="text-base font-bold text-white mb-4 flex items-center gap-2">
        <i class="fas fa-plus-circle text-primary"></i> Add Movie / TV Show
      </h3>
      <?php if ($msg === 'movie-added'): ?><div class="alert alert-success mb-3"><i class="fas fa-check mr-1"></i>Added!</div><?php endif; ?>
      <form method="post" action="actions.php">
        <input type="hidden" name="action" value="save_movie">
        <div class="space-y-3">
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="text-xs text-white/50 block mb-1">TMDB ID *</label>
              <input type="number" name="tmdb_id" required placeholder="e.g. 550" class="input-dark text-sm">
            </div>
            <div>
              <label class="text-xs text-white/50 block mb-1">Type *</label>
              <select name="media_type" class="input-dark text-sm">
                <option value="movie">Movie</option>
                <option value="tv">TV Show</option>
              </select>
            </div>
          </div>
          <div>
            <label class="text-xs text-white/50 block mb-1">Title *</label>
            <input type="text" name="title" required placeholder="Movie title" class="input-dark text-sm">
          </div>
          <div>
            <label class="text-xs text-white/50 block mb-1">Overview</label>
            <textarea name="overview" rows="3" placeholder="Movie description..." class="input-dark text-sm resize-none"></textarea>
          </div>
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="text-xs text-white/50 block mb-1">Poster Path</label>
              <input type="text" name="poster_path" placeholder="/path.jpg or full URL" class="input-dark text-sm">
            </div>
            <div>
              <label class="text-xs text-white/50 block mb-1">Backdrop Path</label>
              <input type="text" name="backdrop_path" placeholder="/path.jpg or full URL" class="input-dark text-sm">
            </div>
          </div>
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="text-xs text-white/50 block mb-1">Release Date</label>
              <input type="date" name="release_date" class="input-dark text-sm">
            </div>
            <div>
              <label class="text-xs text-white/50 block mb-1">Vote Average</label>
              <input type="number" step="0.1" min="0" max="10" name="vote_average" placeholder="7.5" class="input-dark text-sm">
            </div>
          </div>
          <div>
            <label class="text-xs text-white/50 block mb-1">Download Links JSON</label>
            <textarea name="download_links_json" rows="2" placeholder='[{"quality":"1080p","url":"https://..."}]' class="input-dark text-sm resize-none font-mono text-xs">[]</textarea>
          </div>
          <label class="flex items-center gap-2 text-sm text-white/60 cursor-pointer">
            <input type="checkbox" name="is_featured" value="1" class="rounded"> Mark as Featured
          </label>
          <button type="submit" class="btn-primary w-full justify-center py-2.5">
            <i class="fas fa-plus"></i> Add to Library
          </button>
        </div>
      </form>
    </div>

    <!-- TMDB Import (search) in add-post tab too -->
    <div class="neu-flat rounded-xl p-5">
      <h3 class="text-base font-bold text-white mb-4 flex items-center gap-2">
        <i class="fas fa-cloud-download-alt text-primary"></i> Import from TMDB
      </h3>
      <form method="GET" class="flex gap-2 mb-4">
        <input type="hidden" name="tab" value="add-post">
        <input type="text" name="tmdb_q" value="<?= htmlspecialchars($tmdbSearchQuery, ENT_QUOTES, 'UTF-8') ?>" placeholder="Search TMDB title..." class="input-dark text-sm flex-1">
        <select name="tmdb_type" class="input-dark text-sm w-auto">
          <option value="multi" <?= $tmdbSearchType==='multi'?'selected':'' ?>>All</option>
          <option value="movie" <?= $tmdbSearchType==='movie'?'selected':'' ?>>Movie</option>
          <option value="tv" <?= $tmdbSearchType==='tv'?'selected':'' ?>>TV</option>
        </select>
        <button type="submit" class="btn-primary text-sm py-2 px-3"><i class="fas fa-search"></i></button>
      </form>
      <?php if (!empty($tmdbSearchResults)): ?>
      <div class="space-y-2 max-h-96 overflow-y-auto pr-1" style="scrollbar-width:thin">
        <?php foreach (array_slice($tmdbSearchResults, 0, 10) as $r): ?>
        <?php
        $rId    = (int)$r['id'];
        $rType  = $r['media_type'] ?? ($tmdbSearchType === 'movie' ? 'movie' : 'tv');
        $rTitle = $r['title'] ?? $r['name'] ?? 'Untitled';
        $rRating= (float)($r['vote_average'] ?? 0);
        ?>
        <div class="flex items-center gap-3 p-2 rounded-lg hover:bg-white/5 transition-colors">
          <img src="<?= htmlspecialchars(posterUrl($r['poster_path'] ?? null), ENT_QUOTES, 'UTF-8') ?>" class="w-10 rounded flex-shrink-0" style="aspect-ratio:2/3;object-fit:cover" loading="lazy" alt="">
          <div class="flex-1 min-w-0">
            <p class="text-sm font-medium text-white truncate"><?= htmlspecialchars($rTitle, ENT_QUOTES, 'UTF-8') ?></p>
            <p class="text-xs text-white/30"><?= strtoupper($rType) ?> <?php if ($rRating > 0): ?>• <?= number_format($rRating, 1) ?>★<?php endif; ?></p>
          </div>
          <form method="post" action="actions.php" class="flex-shrink-0">
            <input type="hidden" name="action" value="import_tmdb">
            <input type="hidden" name="tmdb_id" value="<?= $rId ?>">
            <input type="hidden" name="media_type" value="<?= htmlspecialchars($rType, ENT_QUOTES, 'UTF-8') ?>">
            <input type="hidden" name="title" value="<?= htmlspecialchars($rTitle, ENT_QUOTES, 'UTF-8') ?>">
            <input type="hidden" name="overview" value="<?= htmlspecialchars($r['overview'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            <input type="hidden" name="poster_path" value="<?= htmlspecialchars($r['poster_path'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            <input type="hidden" name="backdrop_path" value="<?= htmlspecialchars($r['backdrop_path'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            <input type="hidden" name="release_date" value="<?= htmlspecialchars($r['release_date'] ?? $r['first_air_date'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            <input type="hidden" name="vote_average" value="<?= $rRating ?>">
            <button type="submit" class="btn-primary text-xs py-1.5 px-3"><i class="fas fa-plus"></i></button>
          </form>
        </div>
        <?php endforeach; ?>
      </div>
      <?php elseif ($tmdbSearchQuery): ?>
      <p class="text-sm text-white/40">No results for "<?= htmlspecialchars($tmdbSearchQuery, ENT_QUOTES, 'UTF-8') ?>"</p>
      <?php else: ?>
      <p class="text-sm text-white/30 text-center py-8">Search TMDB to find and import titles.</p>
      <?php endif; ?>
    </div>
  </div>

  <!-- ==================== WIDGETS TAB ==================== -->
  <?php elseif ($tab === 'widgets'): ?>
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Add widget -->
    <div class="neu-flat rounded-xl p-5">
      <h3 class="text-base font-bold text-white mb-4 flex items-center gap-2">
        <i class="fas fa-plus-circle text-primary"></i> Add Widget
      </h3>
      <form method="post" action="actions.php">
        <input type="hidden" name="action" value="save_widget">
        <div class="space-y-3">
          <div>
            <label class="text-xs text-white/50 block mb-1">Type</label>
            <select name="type" class="input-dark text-sm">
              <option value="content_row">Content Row</option>
              <option value="cta_banner">CTA Banner</option>
              <option value="menu_links">Menu Links</option>
            </select>
          </div>
          <div><label class="text-xs text-white/50 block mb-1">Title</label><input type="text" name="title" placeholder="Widget title" class="input-dark text-sm"></div>
          <div><label class="text-xs text-white/50 block mb-1">Config JSON</label><textarea name="config_json" rows="4" placeholder='{"source":"trending"}' class="input-dark text-sm resize-none font-mono text-xs">{}</textarea></div>
          <div class="grid grid-cols-2 gap-3">
            <div><label class="text-xs text-white/50 block mb-1">Sort Order</label><input type="number" name="sort_order" value="0" class="input-dark text-sm"></div>
            <div class="flex items-end pb-1"><label class="flex items-center gap-2 text-sm text-white/60 cursor-pointer"><input type="checkbox" name="is_active" value="1" checked class="rounded"> Active</label></div>
          </div>
          <button type="submit" class="btn-primary w-full justify-center py-2.5"><i class="fas fa-plus"></i> Add Widget</button>
        </div>
      </form>
    </div>
    <!-- Widget list -->
    <div class="space-y-3">
      <?php foreach ($widgets as $w): ?>
      <div class="neu-flat rounded-xl p-4 flex items-start gap-3">
        <div class="flex-1 min-w-0">
          <div class="flex items-center gap-2 mb-0.5">
            <span class="text-xs font-bold text-primary uppercase"><?= htmlspecialchars($w['type'], ENT_QUOTES, 'UTF-8') ?></span>
            <?php if (!$w['is_active']): ?><span class="text-xs text-white/30">(inactive)</span><?php endif; ?>
          </div>
          <p class="text-sm font-medium text-white"><?= htmlspecialchars($w['title'] ?? '(no title)', ENT_QUOTES, 'UTF-8') ?></p>
          <p class="text-xs text-white/30 font-mono truncate"><?= htmlspecialchars(substr($w['config_json'] ?? '', 0, 60), ENT_QUOTES, 'UTF-8') ?></p>
        </div>
        <form method="post" action="actions.php" onsubmit="return confirm('Delete widget?')">
          <input type="hidden" name="action" value="delete_widget">
          <input type="hidden" name="id" value="<?= (int)$w['id'] ?>">
          <button type="submit" class="text-white/40 hover:text-red-400 transition-colors p-1"><i class="fas fa-trash text-sm"></i></button>
        </form>
      </div>
      <?php endforeach; ?>
      <?php if (empty($widgets)): ?><p class="text-sm text-white/30 text-center py-8">No widgets yet.</p><?php endif; ?>
    </div>
  </div>

  <!-- ==================== SERVERS TAB ==================== -->
  <?php elseif ($tab === 'servers'): ?>
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Add / Edit server form -->
    <div class="neu-flat rounded-xl p-5">
      <h3 class="text-base font-bold text-white mb-4 flex items-center gap-2">
        <i class="fas fa-server text-primary"></i>
        <?= $editServer ? 'Edit Server' : 'Add Server' ?>
        <?php if ($editServer): ?><a href="?tab=servers" class="ml-auto text-xs text-white/40 hover:text-white"><i class="fas fa-plus mr-1"></i>New</a><?php endif; ?>
      </h3>
      <form method="post" action="actions.php">
        <input type="hidden" name="action" value="save_server">
        <?php if ($editServer): ?><input type="hidden" name="id" value="<?= (int)$editServer['id'] ?>"><?php endif; ?>
        <div class="space-y-3">
          <div><label class="text-xs text-white/50 block mb-1">Name *</label><input type="text" name="name" value="<?= htmlspecialchars($editServer['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required placeholder="Server name" class="input-dark text-sm"></div>
          <div>
            <label class="text-xs text-white/50 block mb-1">Type</label>
            <select name="type" class="input-dark text-sm">
              <option value="stream" <?= ($editServer['type'] ?? 'stream') === 'stream' ? 'selected' : '' ?>>Streaming</option>
              <option value="download" <?= ($editServer['type'] ?? '') === 'download' ? 'selected' : '' ?>>Download</option>
            </select>
          </div>
          <div><label class="text-xs text-white/50 block mb-1">Movie URL (use {id})</label><input type="text" name="url" value="<?= htmlspecialchars($editServer['url'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required placeholder="https://example.com/embed/movie/{id}" class="input-dark text-sm font-mono text-xs"></div>
          <div><label class="text-xs text-white/50 block mb-1">TV URL (use {id}/{season}/{episode})</label><input type="text" name="url_tv" value="<?= htmlspecialchars($editServer['url_tv'] ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="https://example.com/embed/tv/{id}/{season}/{episode}" class="input-dark text-sm font-mono text-xs"></div>
          <div><label class="text-xs text-white/50 block mb-1">Description</label><input type="text" name="description" value="<?= htmlspecialchars($editServer['description'] ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="Optional description" class="input-dark text-sm"></div>
          <div class="flex flex-wrap gap-4">
            <?php
            $checks = [['has_ads','Has Ads'],['has_4k','4K Quality'],['is_download','Is Download'],['is_active','Active']];
            foreach ($checks as [$n,$l]):
            $checked = isset($editServer) ? (int)($editServer[$n] ?? 0) : ($n === 'is_active' ? 1 : 0);
            ?>
            <label class="flex items-center gap-2 text-sm text-white/60 cursor-pointer">
              <input type="checkbox" name="<?= $n ?>" value="1" <?= $checked ? 'checked' : '' ?> class="rounded"><?= $l ?>
            </label>
            <?php endforeach; ?>
          </div>
          <button type="submit" class="btn-primary w-full justify-center py-2.5">
            <i class="fas fa-save"></i> <?= $editServer ? 'Update Server' : 'Add Server' ?>
          </button>
        </div>
      </form>
    </div>
    <!-- Server list -->
    <div class="space-y-3">
      <?php foreach ($servers as $srv): ?>
      <div class="neu-flat rounded-xl p-4">
        <div class="flex items-start justify-between gap-3">
          <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2 mb-1">
              <span class="font-semibold text-white text-sm"><?= htmlspecialchars($srv['name'], ENT_QUOTES, 'UTF-8') ?></span>
              <span class="text-xs px-1.5 py-0.5 rounded border border-white/10 <?= $srv['type'] === 'download' ? 'text-blue-400 border-blue-400/30' : 'text-green-400 border-green-400/30' ?>"><?= $srv['type'] ?></span>
              <?php if (!$srv['is_active']): ?><span class="text-xs text-white/30">inactive</span><?php endif; ?>
              <?php if ($srv['has_4k']): ?><span class="text-xs text-yellow-400">4K</span><?php endif; ?>
              <?php if ($srv['has_ads']): ?><span class="text-xs text-white/30">ads</span><?php endif; ?>
            </div>
            <p class="text-xs text-white/30 font-mono truncate"><?= htmlspecialchars($srv['url'], ENT_QUOTES, 'UTF-8') ?></p>
          </div>
          <div class="flex gap-2 flex-shrink-0">
            <a href="?tab=servers&edit_server=<?= (int)$srv['id'] ?>" class="text-white/40 hover:text-primary transition-colors p-1"><i class="fas fa-edit text-sm"></i></a>
            <form method="post" action="actions.php" onsubmit="return confirm('Delete server?')">
              <input type="hidden" name="action" value="delete_server">
              <input type="hidden" name="id" value="<?= (int)$srv['id'] ?>">
              <button type="submit" class="text-white/40 hover:text-red-400 transition-colors p-1"><i class="fas fa-trash text-sm"></i></button>
            </form>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
      <?php if (empty($servers)): ?><p class="text-sm text-white/30 text-center py-8">No servers yet.</p><?php endif; ?>
    </div>
  </div>

  <!-- ==================== PAGES TAB ==================== -->
  <?php elseif ($tab === 'pages'): ?>
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Add / Edit page form -->
    <div class="neu-flat rounded-xl p-5">
      <h3 class="text-base font-bold text-white mb-4 flex items-center gap-2">
        <i class="fas fa-file-alt text-primary"></i> <?= $editPage ? 'Edit Page' : 'Add Page' ?>
        <?php if ($editPage): ?><a href="?tab=pages" class="ml-auto text-xs text-white/40 hover:text-white"><i class="fas fa-plus mr-1"></i>New</a><?php endif; ?>
      </h3>
      <form method="post" action="actions.php">
        <input type="hidden" name="action" value="save_page">
        <div class="space-y-3">
          <div><label class="text-xs text-white/50 block mb-1">Title *</label><input type="text" name="title" value="<?= htmlspecialchars($editPage['title'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required placeholder="Page title" class="input-dark text-sm"></div>
          <div><label class="text-xs text-white/50 block mb-1">Slug (auto-generated if empty)</label><input type="text" name="slug" value="<?= htmlspecialchars($editPage['slug'] ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="page-slug" class="input-dark text-sm font-mono"></div>
          <div><label class="text-xs text-white/50 block mb-1">Content *</label><textarea name="content" rows="8" required placeholder="Page content (HTML allowed)" class="input-dark text-sm resize-none"><?= htmlspecialchars($editPage['content'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea></div>
          <div>
            <label class="text-xs text-white/50 block mb-1">Sort Order</label>
            <input type="number" name="sort_order" value="<?= (int)($editPage['sort_order'] ?? 0) ?>" class="input-dark text-sm">
          </div>
          <div class="flex flex-wrap gap-4">
            <label class="flex items-center gap-2 text-sm text-white/60 cursor-pointer"><input type="checkbox" name="is_active" value="1" <?= ($editPage['is_active'] ?? 1) ? 'checked' : '' ?> class="rounded">Active</label>
            <label class="flex items-center gap-2 text-sm text-white/60 cursor-pointer"><input type="checkbox" name="show_in_footer" value="1" <?= ($editPage['show_in_footer'] ?? 1) ? 'checked' : '' ?> class="rounded">Show in Footer</label>
          </div>
          <button type="submit" class="btn-primary w-full justify-center py-2.5"><i class="fas fa-save"></i> <?= $editPage ? 'Update Page' : 'Save Page' ?></button>
        </div>
      </form>
    </div>
    <!-- Page list -->
    <div class="space-y-3">
      <?php foreach ($pages as $pg): ?>
      <div class="neu-flat rounded-xl p-4 flex items-center gap-3">
        <div class="flex-1 min-w-0">
          <p class="font-medium text-white text-sm"><?= htmlspecialchars($pg['title'], ENT_QUOTES, 'UTF-8') ?></p>
          <p class="text-xs text-white/30">/page.php?slug=<?= htmlspecialchars($pg['slug'], ENT_QUOTES, 'UTF-8') ?></p>
        </div>
        <div class="flex gap-2 flex-shrink-0">
          <a href="page.php?slug=<?= htmlspecialchars($pg['slug'], ENT_QUOTES, 'UTF-8') ?>" target="_blank" class="text-white/40 hover:text-primary transition-colors p-1"><i class="fas fa-eye text-sm"></i></a>
          <a href="?tab=pages&edit_page=<?= (int)$pg['id'] ?>" class="text-white/40 hover:text-primary transition-colors p-1"><i class="fas fa-edit text-sm"></i></a>
          <form method="post" action="actions.php" onsubmit="return confirm('Delete page?')">
            <input type="hidden" name="action" value="delete_page">
            <input type="hidden" name="id" value="<?= (int)$pg['id'] ?>">
            <button type="submit" class="text-white/40 hover:text-red-400 transition-colors p-1"><i class="fas fa-trash text-sm"></i></button>
          </form>
        </div>
      </div>
      <?php endforeach; ?>
      <?php if (empty($pages)): ?><p class="text-sm text-white/30 text-center py-8">No pages yet.</p><?php endif; ?>
    </div>
  </div>

  <!-- ==================== UPI TAB ==================== -->
  <?php elseif ($tab === 'upi'): ?>
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- UPI Settings quick update -->
    <div class="neu-flat rounded-xl p-5">
      <h3 class="text-base font-bold text-white mb-4 flex items-center gap-2"><i class="fas fa-qrcode text-primary"></i> UPI Settings</h3>
      <form method="post" action="actions.php">
        <input type="hidden" name="action" value="save_many_settings">
        <div class="space-y-3">
          <div><label class="text-xs text-white/50 block mb-1">UPI ID</label><input type="text" name="upi_id" value="<?= htmlspecialchars(s($pdo,'upi_id'), ENT_QUOTES, 'UTF-8') ?>" placeholder="yourname@upi" class="input-dark text-sm"></div>
          <div><label class="text-xs text-white/50 block mb-1">QR Image URL</label><input type="text" name="upi_qr_url" value="<?= htmlspecialchars(s($pdo,'upi_qr_url'), ENT_QUOTES, 'UTF-8') ?>" placeholder="https://..." class="input-dark text-sm"></div>
          <button type="submit" class="btn-primary w-full justify-center py-2"><i class="fas fa-save"></i> Save</button>
        </div>
      </form>
    </div>
    <!-- Payments list -->
    <div class="lg:col-span-2">
      <h3 class="text-base font-bold text-white mb-4 flex items-center gap-2"><i class="fas fa-list text-primary"></i> Payments (<?= count($payments) ?>)</h3>
      <div class="space-y-3">
        <?php foreach ($payments as $pay): ?>
        <div class="neu-flat rounded-xl p-4">
          <div class="flex flex-wrap items-start justify-between gap-3">
            <div>
              <p class="font-semibold text-white text-sm"><?= htmlspecialchars($pay['user_name'] ?? '', ENT_QUOTES, 'UTF-8') ?></p>
              <p class="text-xs text-white/40"><?= htmlspecialchars($pay['user_email'] ?? '', ENT_QUOTES, 'UTF-8') ?></p>
              <p class="text-xs text-white/30 mt-0.5">TXN: <?= htmlspecialchars($pay['transaction_id'] ?? '', ENT_QUOTES, 'UTF-8') ?> • ₹<?= number_format((float)($pay['amount'] ?? 0), 0) ?></p>
            </div>
            <form method="post" action="actions.php" class="flex items-center gap-2">
              <input type="hidden" name="action" value="update_upi_payment_status">
              <input type="hidden" name="id" value="<?= (int)$pay['id'] ?>">
              <select name="status" class="input-dark text-xs py-1.5 w-auto">
                <option value="pending" <?= ($pay['status']??'')=='pending'?'selected':'' ?>>Pending</option>
                <option value="approved" <?= ($pay['status']??'')=='approved'?'selected':'' ?>>Approved</option>
                <option value="rejected" <?= ($pay['status']??'')=='rejected'?'selected':'' ?>>Rejected</option>
              </select>
              <input type="text" name="admin_note" value="<?= htmlspecialchars($pay['admin_note'] ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="Note" class="input-dark text-xs py-1.5 w-28">
              <button type="submit" class="btn-primary text-xs py-1.5 px-3"><i class="fas fa-check"></i></button>
            </form>
          </div>
          <?php
          $statusColors = ['pending'=>'text-yellow-400','approved'=>'text-green-400','rejected'=>'text-red-400'];
          $statusColor  = $statusColors[$pay['status'] ?? 'pending'] ?? 'text-white/40';
          ?>
          <span class="text-xs <?= $statusColor ?> mt-1 inline-block capitalize"><?= htmlspecialchars($pay['status'] ?? 'pending', ENT_QUOTES, 'UTF-8') ?></span>
        </div>
        <?php endforeach; ?>
        <?php if (empty($payments)): ?><p class="text-sm text-white/30 text-center py-8">No payments yet.</p><?php endif; ?>
      </div>
    </div>
  </div>

  <!-- ==================== SETTINGS TAB ==================== -->
  <?php elseif ($tab === 'settings'): ?>
  <form method="post" action="actions.php">
    <input type="hidden" name="action" value="save_many_settings">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

      <!-- Site / SEO -->
      <div class="neu-flat rounded-xl p-5 space-y-4">
        <h3 class="text-base font-bold text-white flex items-center gap-2"><i class="fas fa-globe text-primary"></i> Site & SEO</h3>
        <?php
        $siteFields = [
          ['site_title','Site Title','text','TMovie PHP'],
          ['site_description','Site Description','text','Watch trending movies and TV shows'],
          ['keywords','SEO Keywords','text','movies, tv shows, streaming'],
          ['footer_text','Footer Text','text','Your ultimate destination for movies and TV shows'],
          ['favicon_url','Favicon URL','text','https://...'],
          ['logo_url','Logo URL','text','https://...'],
          ['og_image','OG Image URL','text','https://...'],
        ];
        foreach ($siteFields as [$key,$label,$type,$ph]):
        ?>
        <div>
          <label class="text-xs text-white/50 block mb-1"><?= $label ?></label>
          <input type="<?= $type ?>" name="<?= $key ?>" value="<?= htmlspecialchars(s($pdo,$key), ENT_QUOTES, 'UTF-8') ?>" placeholder="<?= htmlspecialchars($ph, ENT_QUOTES, 'UTF-8') ?>" class="input-dark text-sm">
        </div>
        <?php endforeach; ?>
      </div>

      <!-- Social Links -->
      <div class="neu-flat rounded-xl p-5 space-y-4">
        <h3 class="text-base font-bold text-white flex items-center gap-2"><i class="fas fa-share-alt text-primary"></i> Social Links</h3>
        <?php
        $socialFields = [
          ['telegram_link','Telegram','https://t.me/...'],
          ['twitter_link','Twitter / X','https://twitter.com/...'],
          ['instagram_link','Instagram','https://instagram.com/...'],
          ['youtube_link','YouTube','https://youtube.com/...'],
          ['facebook_link','Facebook','https://facebook.com/...'],
        ];
        foreach ($socialFields as [$key,$label,$ph]):
        ?>
        <div>
          <label class="text-xs text-white/50 block mb-1"><?= $label ?></label>
          <input type="url" name="<?= $key ?>" value="<?= htmlspecialchars(s($pdo,$key), ENT_QUOTES, 'UTF-8') ?>" placeholder="<?= htmlspecialchars($ph, ENT_QUOTES, 'UTF-8') ?>" class="input-dark text-sm">
        </div>
        <?php endforeach; ?>
      </div>

      <!-- Analytics -->
      <div class="neu-flat rounded-xl p-5 space-y-4">
        <h3 class="text-base font-bold text-white flex items-center gap-2"><i class="fas fa-chart-bar text-primary"></i> Analytics</h3>
        <div>
          <label class="text-xs text-white/50 block mb-1">Google Analytics ID (G-XXXXXXX)</label>
          <input type="text" name="google_analytics_id" value="<?= htmlspecialchars(s($pdo,'google_analytics_id'), ENT_QUOTES, 'UTF-8') ?>" placeholder="G-XXXXXXXXXX" class="input-dark text-sm font-mono">
        </div>
        <div>
          <label class="text-xs text-white/50 block mb-1">Search Console Meta Tag</label>
          <input type="text" name="search_console_meta" value="<?= htmlspecialchars(s($pdo,'search_console_meta'), ENT_QUOTES, 'UTF-8') ?>" placeholder='<meta name="google-site-verification" content="...">' class="input-dark text-sm font-mono text-xs">
        </div>
        <div>
          <label class="text-xs text-white/50 block mb-1">Admin Email</label>
          <input type="email" name="admin_email" value="<?= htmlspecialchars(s($pdo,'admin_email'), ENT_QUOTES, 'UTF-8') ?>" placeholder="admin@example.com" class="input-dark text-sm">
        </div>
      </div>

      <!-- Razorpay / Subscription -->
      <div class="neu-flat rounded-xl p-5 space-y-4">
        <h3 class="text-base font-bold text-white flex items-center gap-2"><i class="fas fa-credit-card text-primary"></i> Payment & Subscription</h3>
        <div>
          <label class="text-xs text-white/50 block mb-1">Razorpay Key ID</label>
          <input type="text" name="razorpay_key_id" value="<?= htmlspecialchars(s($pdo,'razorpay_key_id'), ENT_QUOTES, 'UTF-8') ?>" placeholder="rzp_live_..." class="input-dark text-sm font-mono">
        </div>
        <div>
          <label class="text-xs text-white/50 block mb-1">Razorpay Key Secret</label>
          <input type="password" name="razorpay_key_secret" value="<?= htmlspecialchars(s($pdo,'razorpay_key_secret'), ENT_QUOTES, 'UTF-8') ?>" placeholder="••••••••••" class="input-dark text-sm font-mono">
        </div>
        <div>
          <label class="text-xs text-white/50 block mb-1">UPI ID</label>
          <input type="text" name="upi_id" value="<?= htmlspecialchars(s($pdo,'upi_id'), ENT_QUOTES, 'UTF-8') ?>" placeholder="yourname@upi" class="input-dark text-sm">
        </div>
        <div>
          <label class="text-xs text-white/50 block mb-1">UPI QR Image URL</label>
          <input type="url" name="upi_qr_url" value="<?= htmlspecialchars(s($pdo,'upi_qr_url'), ENT_QUOTES, 'UTF-8') ?>" placeholder="https://..." class="input-dark text-sm">
        </div>
        <label class="flex items-center gap-2 text-sm text-white/60 cursor-pointer">
          <input type="checkbox" name="subscription_enabled" value="1" <?= s($pdo,'subscription_enabled') === '1' ? 'checked' : '' ?> class="rounded"> Enable Subscription
        </label>
      </div>

      <!-- Video Ads -->
      <div class="neu-flat rounded-xl p-5 space-y-4">
        <h3 class="text-base font-bold text-white flex items-center gap-2"><i class="fas fa-ad text-primary"></i> Video Ads</h3>
        <label class="flex items-center gap-2 text-sm text-white/60 cursor-pointer">
          <input type="checkbox" name="video_ad_enabled" value="1" <?= s($pdo,'video_ad_enabled') === '1' ? 'checked' : '' ?> class="rounded"> Enable Video Ads
        </label>
        <div>
          <label class="text-xs text-white/50 block mb-1">Ad Type</label>
          <select name="video_ad_type" class="input-dark text-sm">
            <option value="image" <?= s($pdo,'video_ad_type')==='image'?'selected':'' ?>>Image</option>
            <option value="video" <?= s($pdo,'video_ad_type')==='video'?'selected':'' ?>>Video</option>
          </select>
        </div>
        <div>
          <label class="text-xs text-white/50 block mb-1">Ad URL</label>
          <input type="url" name="video_ad_url" value="<?= htmlspecialchars(s($pdo,'video_ad_url'), ENT_QUOTES, 'UTF-8') ?>" placeholder="https://..." class="input-dark text-sm">
        </div>
        <div>
          <label class="text-xs text-white/50 block mb-1">Skip After (seconds)</label>
          <input type="number" name="video_ad_skip_seconds" value="<?= htmlspecialchars(s($pdo,'video_ad_skip_seconds') ?: '5', ENT_QUOTES, 'UTF-8') ?>" min="0" class="input-dark text-sm">
        </div>
      </div>

      <!-- Change Password -->
      <div class="neu-flat rounded-xl p-5 space-y-4">
        <h3 class="text-base font-bold text-white flex items-center gap-2"><i class="fas fa-key text-primary"></i> Change Password</h3>
        <form method="post" action="actions.php" onsubmit="return validatePw()">
          <input type="hidden" name="action" value="change_password">
          <div class="space-y-3">
            <div>
              <label class="text-xs text-white/50 block mb-1">New Password</label>
              <input type="password" name="new_password" id="newPw" placeholder="••••••••" class="input-dark text-sm">
            </div>
            <div>
              <label class="text-xs text-white/50 block mb-1">Confirm Password</label>
              <input type="password" name="confirm_password" id="confirmPw" placeholder="••••••••" class="input-dark text-sm">
            </div>
            <button type="submit" class="btn-accent w-full justify-center py-2"><i class="fas fa-key"></i> Change Password</button>
          </div>
        </form>
      </div>
    </div>

    <div class="mt-6 flex justify-end">
      <button type="submit" class="btn-primary py-3 px-8 text-base">
        <i class="fas fa-save"></i> Save All Settings
      </button>
    </div>
  </form>
  <?php endif; ?>

</main>

<script>
function validatePw(){
  var np = document.getElementById('newPw');
  var cp = document.getElementById('confirmPw');
  if(np && cp && np.value !== cp.value){ alert('Passwords do not match!'); return false; }
  if(np && np.value.length < 6){ alert('Password must be at least 6 characters.'); return false; }
  return true;
}
</script>

<?php layout_footer($pdo); ?>
