<?php
require __DIR__ . '/includes/db.php';
require __DIR__ . '/includes/helpers.php';
require __DIR__ . '/includes/tmdb.php';
require __DIR__ . '/includes/layout.php';

$config = require __DIR__ . '/config.php';

$mediaType = $_GET['type'] ?? 'movie';
$tmdbId    = (int)($_GET['id'] ?? 0);
$season    = (int)($_GET['season'] ?? 1);
$episode   = (int)($_GET['episode'] ?? 1);

if (!$tmdbId) { header('Location: index.php'); exit; }

// Fetch from DB
$stmt = $pdo->prepare('SELECT * FROM movies WHERE tmdb_id=? AND media_type=? AND is_active=1 LIMIT 1');
$stmt->execute([$tmdbId, $mediaType]);
$dbMovie = $stmt->fetch();

// Fallback to TMDB API
$tmdbData = null;
if (!$dbMovie) {
    $tmdbData = fetchDetails($config, $mediaType, $tmdbId);
}

// Merge data
if ($dbMovie) {
    $title    = $dbMovie['title'];
    $overview = $dbMovie['overview'] ?? '';
    $poster   = posterUrl($dbMovie['poster_path']);
    $backdrop = backdropUrl($dbMovie['backdrop_path']);
    $rating   = (float)($dbMovie['vote_average'] ?? 0);
    $runtime  = (int)($dbMovie['runtime'] ?? 0);
    $relDate  = $dbMovie['release_date'] ?? '';
    $genres   = json_decode($dbMovie['genres_json'] ?? '[]', true) ?: [];
    $cast     = json_decode($dbMovie['cast_json'] ?? '[]', true) ?: [];
    $videos   = json_decode($dbMovie['videos_json'] ?? '[]', true) ?: [];
    $seasons  = json_decode($dbMovie['seasons_json'] ?? '[]', true) ?: [];
    $dlLinks  = json_decode($dbMovie['download_links_json'] ?? '[]', true) ?: [];
} elseif ($tmdbData) {
    $title    = $tmdbData['title'] ?? $tmdbData['name'] ?? 'Untitled';
    $overview = $tmdbData['overview'] ?? '';
    $poster   = posterUrl($tmdbData['poster_path'] ?? null);
    $backdrop = backdropUrl($tmdbData['backdrop_path'] ?? null);
    $rating   = (float)($tmdbData['vote_average'] ?? 0);
    $runtime  = (int)($tmdbData['runtime'] ?? $tmdbData['episode_run_time'][0] ?? 0);
    $relDate  = $tmdbData['release_date'] ?? $tmdbData['first_air_date'] ?? '';
    $genres   = array_column($tmdbData['genres'] ?? [], 'name');
    $cast     = array_map(fn($c) => ['name'=>$c['name'],'character'=>$c['character']??'','profile_path'=>$c['profile_path']??null], array_slice($tmdbData['credits']['cast'] ?? [], 0, 12));
    $rawVids  = $tmdbData['videos']['results'] ?? [];
    $videos   = array_map(fn($v) => ['key'=>$v['key'],'type'=>$v['type'],'site'=>$v['site']], $rawVids);
    $seasons  = [];
    foreach ($tmdbData['seasons'] ?? [] as $s) {
        if ((int)$s['season_number'] > 0) $seasons[] = ['season_number'=>$s['season_number'],'name'=>$s['name'],'episode_count'=>$s['episode_count']];
    }
    $dlLinks = [];
} else {
    header('Location: index.php'); exit;
}

$year = $relDate ? substr($relDate, 0, 4) : '';

// Find YouTube trailer
$trailer = null;
foreach ($videos as $v) {
    if (($v['site'] ?? '') === 'YouTube' && ($v['type'] ?? '') === 'Trailer') { $trailer = $v['key']; break; }
}
if (!$trailer) {
    foreach ($videos as $v) {
        if (($v['site'] ?? '') === 'YouTube') { $trailer = $v['key']; break; }
    }
}

// Fetch streaming + download servers
$servers  = $pdo->query('SELECT * FROM custom_servers WHERE is_active=1 AND is_download=0 ORDER BY is_default DESC, sort_order ASC, id ASC')->fetchAll();
$dlServers= $pdo->query('SELECT * FROM custom_servers WHERE is_active=1 AND is_download=1 ORDER BY sort_order ASC, id ASC')->fetchAll();

// Active server
$activeServerId = (int)($_GET['server'] ?? ($servers[0]['id'] ?? 0));
$activeServer = null;
foreach ($servers as $s) { if ((int)$s['id'] === $activeServerId) { $activeServer = $s; break; } }
if (!$activeServer && !empty($servers)) $activeServer = $servers[0];

$embedUrl = $activeServer ? serverBuildUrl($activeServer, $tmdbId, $mediaType, $season, $episode) : '';

// Watchlist status
$inWatchlist = $pdo->prepare('SELECT id FROM watchlist WHERE tmdb_id=? AND media_type=? LIMIT 1');
$inWatchlist->execute([$tmdbId, $mediaType]);
$watchlistRow = $inWatchlist->fetch();
$watchlistId  = $watchlistRow ? (int)$watchlistRow['id'] : 0;

// Related
$related = [];
if ($tmdbData && isset($tmdbData['similar']['results'])) {
    $related = array_slice($tmdbData['similar']['results'], 0, 12);
} else {
    $relStmt = $pdo->prepare('SELECT * FROM movies WHERE is_active=1 AND media_type=? AND tmdb_id != ? ORDER BY vote_average DESC LIMIT 12');
    $relStmt->execute([$mediaType, $tmdbId]);
    $related = $relStmt->fetchAll();
}

// Watch providers
$providers = [];
if ($tmdbData && isset($tmdbData['watch/providers']['results'])) {
    $wp = $tmdbData['watch/providers']['results'];
    $countryData = $wp['US'] ?? $wp['IN'] ?? array_values($wp)[0] ?? [];
    $flatrate = $countryData['flatrate'] ?? [];
    $rent     = $countryData['rent'] ?? [];
    $providers = array_slice(array_merge($flatrate, $rent), 0, 6);
}

// Comments
$comments = $pdo->prepare('SELECT * FROM comments WHERE tmdb_id=? AND media_type=? ORDER BY created_at DESC LIMIT 30');
$comments->execute([$tmdbId, $mediaType]);
$comments = $comments->fetchAll();

// Reactions
$reactionEmojis = ['❤️','😂','😮','😢','😡','👍'];
$reactions = $pdo->prepare('SELECT emoji, count FROM reactions WHERE tmdb_id=? AND media_type=?');
$reactions->execute([$tmdbId, $mediaType]);
$reactionMap = [];
foreach ($reactions->fetchAll() as $r) $reactionMap[$r['emoji']] = (int)$r['count'];

// Return URL for actions
$returnUrl = 'watch.php?type=' . urlencode($mediaType) . '&id=' . $tmdbId . '&season=' . $season . '&episode=' . $episode;

// Fetch TV episodes if needed
$tvEpisodes = [];
if ($mediaType === 'tv' && $tmdbId) {
    $epData = tmdbRequest($config, "tv/$tmdbId/season/$season");
    $tvEpisodes = $epData['episodes'] ?? [];
}

layout_head($title, $pdo);
layout_header($pdo);
?>

<div class="relative w-full overflow-hidden" style="height: 280px; min-height: 180px;">
  <img src="<?= htmlspecialchars($backdrop, ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?>" class="absolute inset-0 w-full h-full object-cover">
  <div class="absolute inset-0" style="background:linear-gradient(to bottom,rgba(26,29,46,0.3) 0%,rgba(26,29,46,1) 100%)"></div>
</div>

<main class="max-w-7xl mx-auto px-4 pb-12 -mt-10 relative z-10">
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    <!-- Main Column -->
    <div class="lg:col-span-2 space-y-5">

      <!-- Title + actions -->
      <div class="flex flex-col sm:flex-row sm:items-start gap-4">
        <img src="<?= htmlspecialchars($poster, ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?>"
             class="w-24 rounded-lg flex-shrink-0 shadow-xl hidden sm:block" style="aspect-ratio:2/3;object-fit:cover">
        <div class="flex-1 min-w-0">
          <div class="flex items-center gap-2 mb-1">
            <span class="text-xs font-bold text-primary uppercase tracking-wider"><?= $mediaType === 'tv' ? 'TV Series' : 'Movie' ?></span>
            <?php if ($year): ?><span class="text-xs text-white/40"><?= htmlspecialchars($year, ENT_QUOTES, 'UTF-8') ?></span><?php endif; ?>
            <?php if ($rating > 0): ?><span class="text-xs text-white/60 flex items-center gap-1"><i class="fas fa-star text-yellow-400 text-xs"></i><?= number_format($rating, 1) ?></span><?php endif; ?>
          </div>
          <h1 class="text-2xl md:text-3xl font-black text-white mb-2"><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></h1>
          <?php if (!empty($genres)): ?>
          <div class="flex flex-wrap gap-1.5 mb-3">
            <?php foreach (array_slice($genres, 0, 4) as $g): ?>
            <span class="text-xs px-2 py-0.5 rounded-full border border-white/10 text-white/60"><?= htmlspecialchars($g, ENT_QUOTES, 'UTF-8') ?></span>
            <?php endforeach; ?>
          </div>
          <?php endif; ?>
          <!-- Action buttons -->
          <div class="flex flex-wrap gap-2">
            <?php if ($watchlistId): ?>
            <form method="post" action="actions.php" class="inline">
              <input type="hidden" name="action" value="remove_watchlist">
              <input type="hidden" name="id" value="<?= $watchlistId ?>">
              <button type="submit" class="btn-outline text-sm py-2 px-4">
                <i class="fas fa-bookmark text-primary"></i> Saved
              </button>
            </form>
            <?php else: ?>
            <form method="post" action="actions.php" class="inline">
              <input type="hidden" name="action" value="add_watchlist">
              <input type="hidden" name="tmdb_id" value="<?= $tmdbId ?>">
              <input type="hidden" name="media_type" value="<?= htmlspecialchars($mediaType, ENT_QUOTES, 'UTF-8') ?>">
              <input type="hidden" name="title" value="<?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?>">
              <input type="hidden" name="poster_path" value="<?= htmlspecialchars($dbMovie['poster_path'] ?? $tmdbData['poster_path'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
              <input type="hidden" name="return_url" value="<?= htmlspecialchars($returnUrl, ENT_QUOTES, 'UTF-8') ?>">
              <button type="submit" class="btn-outline text-sm py-2 px-4">
                <i class="far fa-bookmark"></i> Watchlist
              </button>
            </form>
            <?php endif; ?>

            <?php if ($trailer): ?>
            <button onclick="openTrailer('<?= htmlspecialchars($trailer, ENT_QUOTES, 'UTF-8') ?>')" class="btn-outline text-sm py-2 px-4">
              <i class="fab fa-youtube text-red-500"></i> Trailer
            </button>
            <?php endif; ?>
            <button onclick="shareThis()" class="btn-outline text-sm py-2 px-4">
              <i class="fas fa-share-alt"></i> Share
            </button>
          </div>
        </div>
      </div>

      <!-- TV Season/Episode Selector -->
      <?php if ($mediaType === 'tv' && !empty($seasons)): ?>
      <div class="neu-flat rounded-xl p-4">
        <h3 class="text-sm font-bold text-white mb-3 flex items-center gap-2"><i class="fas fa-list text-primary"></i> Episodes</h3>
        <div class="flex flex-wrap gap-2 mb-4">
          <?php foreach ($seasons as $s): ?>
          <?php $sn = (int)$s['season_number']; ?>
          <a href="watch.php?type=tv&id=<?= $tmdbId ?>&season=<?= $sn ?>&episode=1&server=<?= $activeServerId ?>"
             class="text-xs px-3 py-1.5 rounded-lg font-medium transition-all <?= $sn === $season ? 'bg-primary/20 text-primary border border-primary/40' : 'neu-pressed text-white/60 hover:text-white' ?>">
            S<?= $sn ?>
          </a>
          <?php endforeach; ?>
        </div>
        <?php if (!empty($tvEpisodes)): ?>
        <div class="grid grid-cols-1 gap-2 max-h-64 overflow-y-auto pr-1" style="scrollbar-width:thin">
          <?php foreach ($tvEpisodes as $ep): ?>
          <?php $epNum = (int)$ep['episode_number']; ?>
          <a href="watch.php?type=tv&id=<?= $tmdbId ?>&season=<?= $season ?>&episode=<?= $epNum ?>&server=<?= $activeServerId ?>"
             class="flex items-center gap-3 p-2 rounded-lg transition-all <?= $epNum === $episode ? 'bg-primary/10 border border-primary/30' : 'hover:bg-white/5' ?>">
            <span class="text-xs font-bold text-white/40 w-6 flex-shrink-0"><?= $epNum ?></span>
            <?php if (!empty($ep['still_path'])): ?>
            <img src="https://image.tmdb.org/t/p/w185<?= htmlspecialchars($ep['still_path'], ENT_QUOTES, 'UTF-8') ?>" class="w-16 rounded flex-shrink-0" style="aspect-ratio:16/9;object-fit:cover" loading="lazy" alt="">
            <?php else: ?>
            <div class="w-16 rounded flex-shrink-0 bg-white/5 flex items-center justify-center" style="aspect-ratio:16/9"><i class="fas fa-film text-white/20 text-xs"></i></div>
            <?php endif; ?>
            <div class="min-w-0">
              <p class="text-sm font-medium text-white truncate"><?= htmlspecialchars($ep['name'] ?? 'Episode ' . $epNum, ENT_QUOTES, 'UTF-8') ?></p>
              <?php if (!empty($ep['air_date'])): ?><p class="text-xs text-white/30"><?= htmlspecialchars($ep['air_date'], ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
            </div>
          </a>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>
      </div>
      <?php endif; ?>

      <!-- Video Player -->
      <div class="neu-raised rounded-xl overflow-hidden">
        <?php if ($activeServer && $embedUrl): ?>
        <!-- Server selector -->
        <div class="flex items-center gap-2 px-3 py-2 border-b border-white/5 overflow-x-auto" style="scrollbar-width:none">
          <span class="text-xs text-white/40 flex-shrink-0">Servers:</span>
          <?php foreach ($servers as $srv): ?>
          <?php
          $sUrl = 'watch.php?type=' . urlencode($mediaType) . '&id=' . $tmdbId . '&season=' . $season . '&episode=' . $episode . '&server=' . (int)$srv['id'];
          $isActive = (int)$srv['id'] === (int)$activeServer['id'];
          ?>
          <a href="<?= htmlspecialchars($sUrl, ENT_QUOTES, 'UTF-8') ?>"
             class="flex-shrink-0 text-xs px-3 py-1.5 rounded-lg font-medium transition-all <?= $isActive ? 'bg-primary/20 text-primary border border-primary/40' : 'neu-pressed text-white/60 hover:text-white' ?>">
            <?php if ($srv['has_4k']): ?><i class="fas fa-gem text-yellow-400 mr-1 text-xs"></i><?php endif; ?>
            <?= htmlspecialchars($srv['name'], ENT_QUOTES, 'UTF-8') ?>
            <?php if ($srv['has_ads']): ?><span class="text-xs text-white/30 ml-1">(ads)</span><?php endif; ?>
          </a>
          <?php endforeach; ?>
        </div>
        <!-- iFrame player -->
        <div class="relative w-full" style="aspect-ratio:16/9">
          <iframe src="<?= htmlspecialchars($embedUrl, ENT_QUOTES, 'UTF-8') ?>"
                  class="absolute inset-0 w-full h-full border-0"
                  allowfullscreen allow="autoplay; encrypted-media; fullscreen; picture-in-picture"
                  loading="lazy">
          </iframe>
        </div>
        <?php else: ?>
        <div class="flex items-center justify-center bg-black/30" style="aspect-ratio:16/9">
          <div class="text-center">
            <i class="fas fa-video-slash text-4xl text-white/20 mb-3"></i>
            <p class="text-white/40">No streaming server configured.</p>
            <?php if (isLoggedIn()): ?><a href="admin.php?tab=servers" class="btn-primary mt-3 text-sm inline-flex">Add Server</a><?php endif; ?>
          </div>
        </div>
        <?php endif; ?>
      </div>

      <!-- Download Links -->
      <?php if (!empty($dlLinks) || !empty($dlServers)): ?>
      <div class="neu-flat rounded-xl p-4">
        <h3 class="text-sm font-bold text-white mb-3 flex items-center gap-2"><i class="fas fa-download text-primary"></i> Download</h3>
        <div class="flex flex-wrap gap-2">
          <?php foreach ($dlLinks as $dl): ?>
          <a href="<?= htmlspecialchars($dl['url'] ?? '#', ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener"
             class="btn-outline text-sm py-1.5 px-3">
            <i class="fas fa-download"></i> <?= htmlspecialchars($dl['quality'] ?? $dl['label'] ?? 'Download', ENT_QUOTES, 'UTF-8') ?>
          </a>
          <?php endforeach; ?>
          <?php foreach ($dlServers as $ds): ?>
          <?php $dUrl = serverBuildUrl($ds, $tmdbId, $mediaType, $season, $episode); ?>
          <a href="<?= htmlspecialchars($dUrl, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener"
             class="btn-outline text-sm py-1.5 px-3">
            <i class="fas fa-download"></i> <?= htmlspecialchars($ds['name'], ENT_QUOTES, 'UTF-8') ?>
            <?php if ($ds['has_4k']): ?><span class="text-xs text-yellow-400">4K</span><?php endif; ?>
          </a>
          <?php endforeach; ?>
        </div>
      </div>
      <?php endif; ?>

      <!-- Overview -->
      <?php if ($overview): ?>
      <div class="neu-flat rounded-xl p-4">
        <h3 class="text-sm font-bold text-white mb-2">Overview</h3>
        <p class="text-sm text-white/60 leading-relaxed"><?= htmlspecialchars($overview, ENT_QUOTES, 'UTF-8') ?></p>
      </div>
      <?php endif; ?>

      <!-- Cast -->
      <?php if (!empty($cast)): ?>
      <div>
        <h3 class="section-title mb-3">Top Cast</h3>
        <div class="content-row">
          <?php foreach (array_slice($cast, 0, 10) as $actor): ?>
          <div class="card-item text-center">
            <?php if (!empty($actor['profile_path'])): ?>
            <img src="https://image.tmdb.org/t/p/w185<?= htmlspecialchars($actor['profile_path'], ENT_QUOTES, 'UTF-8') ?>"
                 alt="<?= htmlspecialchars($actor['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                 class="w-full rounded-lg" style="aspect-ratio:2/3;object-fit:cover" loading="lazy">
            <?php else: ?>
            <div class="w-full rounded-lg bg-white/5 flex items-center justify-center" style="aspect-ratio:2/3"><i class="fas fa-user text-3xl text-white/20"></i></div>
            <?php endif; ?>
            <p class="text-xs text-white/70 mt-1 truncate font-medium"><?= htmlspecialchars($actor['name'] ?? '', ENT_QUOTES, 'UTF-8') ?></p>
            <?php if (!empty($actor['character'])): ?><p class="text-xs text-white/30 truncate"><?= htmlspecialchars($actor['character'], ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
      <?php endif; ?>

      <!-- Reactions -->
      <div class="neu-flat rounded-xl p-4">
        <h3 class="text-sm font-bold text-white mb-3">Reactions</h3>
        <div class="flex flex-wrap gap-2">
          <?php foreach ($reactionEmojis as $emoji): ?>
          <form method="post" action="actions.php" class="inline">
            <input type="hidden" name="action" value="react">
            <input type="hidden" name="tmdb_id" value="<?= $tmdbId ?>">
            <input type="hidden" name="media_type" value="<?= htmlspecialchars($mediaType, ENT_QUOTES, 'UTF-8') ?>">
            <input type="hidden" name="emoji" value="<?= htmlspecialchars($emoji, ENT_QUOTES, 'UTF-8') ?>">
            <input type="hidden" name="return_url" value="<?= htmlspecialchars($returnUrl, ENT_QUOTES, 'UTF-8') ?>">
            <button type="submit" class="flex items-center gap-1.5 px-3 py-2 rounded-xl neu-pressed text-sm hover:border hover:border-primary/30 transition-all">
              <span><?= $emoji ?></span>
              <span class="text-xs text-white/50"><?= $reactionMap[$emoji] ?? 0 ?></span>
            </button>
          </form>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- Comments -->
      <div class="neu-flat rounded-xl p-4">
        <h3 class="text-sm font-bold text-white mb-4 flex items-center gap-2">
          <i class="fas fa-comments text-primary"></i> Comments
          <span class="text-xs text-white/40 font-normal">(<?= count($comments) ?>)</span>
        </h3>
        <!-- Add comment form -->
        <form method="post" action="actions.php" class="mb-6">
          <input type="hidden" name="action" value="add_comment">
          <input type="hidden" name="tmdb_id" value="<?= $tmdbId ?>">
          <input type="hidden" name="media_type" value="<?= htmlspecialchars($mediaType, ENT_QUOTES, 'UTF-8') ?>">
          <input type="hidden" name="return_url" value="<?= htmlspecialchars($returnUrl . '#comments', ENT_QUOTES, 'UTF-8') ?>">
          <div class="flex gap-3 items-start">
            <div class="w-8 h-8 rounded-full bg-primary/20 flex items-center justify-center flex-shrink-0">
              <i class="fas fa-user text-primary text-xs"></i>
            </div>
            <div class="flex-1 space-y-2">
              <input type="text" name="user_name" placeholder="Your name" required class="input-dark text-sm">
              <textarea name="content" placeholder="Share your thoughts..." required rows="2"
                        class="input-dark text-sm resize-none"></textarea>
              <button type="submit" class="btn-primary text-sm py-1.5 px-4">
                <i class="fas fa-paper-plane"></i> Post
              </button>
            </div>
          </div>
        </form>
        <!-- Comments list -->
        <div class="space-y-4" id="comments">
          <?php foreach ($comments as $c): ?>
          <div class="flex gap-3">
            <div class="w-8 h-8 rounded-full bg-white/5 flex items-center justify-center flex-shrink-0">
              <i class="fas fa-user text-white/30 text-xs"></i>
            </div>
            <div class="flex-1 min-w-0">
              <div class="flex items-center gap-2 mb-1">
                <span class="text-sm font-semibold text-white"><?= htmlspecialchars($c['user_name'] ?? 'Guest', ENT_QUOTES, 'UTF-8') ?></span>
                <span class="text-xs text-white/30"><?= htmlspecialchars(substr($c['created_at'] ?? '', 0, 10), ENT_QUOTES, 'UTF-8') ?></span>
              </div>
              <p class="text-sm text-white/60 leading-relaxed"><?= nl2br(htmlspecialchars($c['content'] ?? '', ENT_QUOTES, 'UTF-8')) ?></p>
            </div>
          </div>
          <?php endforeach; ?>
          <?php if (empty($comments)): ?>
          <p class="text-sm text-white/30 text-center py-4">Be the first to comment!</p>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <!-- Sidebar -->
    <div class="space-y-5">

      <!-- Info panel -->
      <div class="neu-flat rounded-xl p-4">
        <h3 class="text-sm font-bold text-white mb-3">Movie Info</h3>
        <dl class="space-y-2 text-sm">
          <?php if ($rating > 0): ?>
          <div class="flex items-center justify-between">
            <dt class="text-white/40">Rating</dt>
            <dd class="text-white font-medium flex items-center gap-1"><i class="fas fa-star text-yellow-400 text-xs"></i><?= number_format($rating, 1) ?>/10</dd>
          </div>
          <?php endif; ?>
          <?php if ($year): ?>
          <div class="flex items-center justify-between">
            <dt class="text-white/40">Year</dt>
            <dd class="text-white font-medium"><?= htmlspecialchars($year, ENT_QUOTES, 'UTF-8') ?></dd>
          </div>
          <?php endif; ?>
          <?php if ($runtime): ?>
          <div class="flex items-center justify-between">
            <dt class="text-white/40">Runtime</dt>
            <dd class="text-white font-medium"><?= $runtime ?>m</dd>
          </div>
          <?php endif; ?>
          <?php if (!empty($genres)): ?>
          <div>
            <dt class="text-white/40 mb-1">Genres</dt>
            <dd class="flex flex-wrap gap-1">
              <?php foreach (array_slice($genres, 0, 4) as $g): ?>
              <span class="text-xs px-2 py-0.5 rounded-full border border-white/10 text-white/60"><?= htmlspecialchars($g, ENT_QUOTES, 'UTF-8') ?></span>
              <?php endforeach; ?>
            </dd>
          </div>
          <?php endif; ?>
          <?php if ($mediaType === 'tv' && !empty($seasons)): ?>
          <div class="flex items-center justify-between">
            <dt class="text-white/40">Seasons</dt>
            <dd class="text-white font-medium"><?= count($seasons) ?></dd>
          </div>
          <?php endif; ?>
        </dl>
      </div>

      <!-- Watch Providers -->
      <?php if (!empty($providers)): ?>
      <div class="neu-flat rounded-xl p-4">
        <h3 class="text-sm font-bold text-white mb-3">Watch On</h3>
        <div class="flex flex-wrap gap-2">
          <?php foreach ($providers as $p): ?>
          <div class="flex items-center gap-2 px-2 py-1.5 rounded-lg bg-white/5">
            <?php if (!empty($p['logo_path'])): ?>
            <img src="https://image.tmdb.org/t/p/w45<?= htmlspecialchars($p['logo_path'], ENT_QUOTES, 'UTF-8') ?>" class="w-5 h-5 rounded" alt="">
            <?php endif; ?>
            <span class="text-xs text-white/60"><?= htmlspecialchars($p['provider_name'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
      <?php endif; ?>

      <!-- Related -->
      <?php if (!empty($related)): ?>
      <div>
        <h3 class="section-title mb-3">Related</h3>
        <div class="space-y-3">
          <?php foreach (array_slice($related, 0, 6) as $r): ?>
          <?php
          $rId   = isset($r['tmdb_id']) ? (int)$r['tmdb_id'] : (int)($r['id'] ?? 0);
          $rType = $r['media_type'] ?? $mediaType;
          $rTitle= $r['title'] ?? $r['name'] ?? 'Untitled';
          $rRating = (float)($r['vote_average'] ?? 0);
          ?>
          <a href="watch.php?type=<?= htmlspecialchars($rType, ENT_QUOTES, 'UTF-8') ?>&id=<?= $rId ?>"
             class="flex gap-3 items-center group hover:bg-white/5 rounded-lg p-1.5 transition-colors">
            <img src="<?= htmlspecialchars(posterUrl($r['poster_path'] ?? null), ENT_QUOTES, 'UTF-8') ?>"
                 alt="<?= htmlspecialchars($rTitle, ENT_QUOTES, 'UTF-8') ?>"
                 class="w-12 rounded flex-shrink-0" style="aspect-ratio:2/3;object-fit:cover" loading="lazy">
            <div class="min-w-0">
              <p class="text-sm font-medium text-white/80 group-hover:text-white truncate transition-colors"><?= htmlspecialchars($rTitle, ENT_QUOTES, 'UTF-8') ?></p>
              <?php if ($rRating > 0): ?><p class="text-xs text-white/40 flex items-center gap-1"><i class="fas fa-star text-yellow-400 text-xs"></i><?= number_format($rRating, 1) ?></p><?php endif; ?>
            </div>
          </a>
          <?php endforeach; ?>
        </div>
      </div>
      <?php endif; ?>

    </div>
  </div>
</main>

<!-- Trailer Modal -->
<div id="trailerModal" class="fixed inset-0 bg-black/90 z-50 hidden flex items-center justify-center p-4" onclick="if(event.target===this)closeTrailer()">
  <div class="w-full max-w-3xl">
    <div class="flex justify-end mb-2">
      <button onclick="closeTrailer()" class="text-white/60 hover:text-white p-2"><i class="fas fa-times text-xl"></i></button>
    </div>
    <div class="relative" style="aspect-ratio:16/9">
      <iframe id="trailerIframe" class="absolute inset-0 w-full h-full rounded-xl border-0" allowfullscreen></iframe>
    </div>
  </div>
</div>

<script>
function openTrailer(key){
  document.getElementById('trailerIframe').src = 'https://www.youtube.com/embed/' + key + '?autoplay=1';
  document.getElementById('trailerModal').classList.remove('hidden');
  document.getElementById('trailerModal').classList.add('flex');
}
function closeTrailer(){
  document.getElementById('trailerIframe').src = '';
  document.getElementById('trailerModal').classList.add('hidden');
  document.getElementById('trailerModal').classList.remove('flex');
}
function shareThis(){
  if(navigator.share){
    navigator.share({ title: <?= json_encode($title) ?>, url: window.location.href });
  } else if(navigator.clipboard){
    navigator.clipboard.writeText(window.location.href).then(function(){ alert('Link copied!'); });
  } else {
    prompt('Copy link:', window.location.href);
  }
}
document.addEventListener('keydown', function(e){ if(e.key==='Escape') closeTrailer(); });
</script>

<?php layout_footer($pdo); ?>
