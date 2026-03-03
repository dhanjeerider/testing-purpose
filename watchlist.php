<?php
require __DIR__ . '/includes/db.php';
require __DIR__ . '/includes/helpers.php';
require __DIR__ . '/includes/layout.php';

$items = $pdo->query('SELECT * FROM watchlist ORDER BY created_at DESC')->fetchAll();

layout_head('My Watchlist', $pdo);
layout_header($pdo);
?>

<main class="max-w-7xl mx-auto px-4 py-6 pb-12">
  <div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-black text-white flex items-center gap-2">
      <i class="fas fa-bookmark text-primary"></i> My Watchlist
      <span class="text-sm font-normal text-white/40">(<?= count($items) ?>)</span>
    </h1>
    <?php if (!empty($items)): ?>
    <a href="search.php" class="btn-outline text-sm py-2 px-4">
      <i class="fas fa-plus"></i> Add More
    </a>
    <?php endif; ?>
  </div>

  <?php if (!empty($items)): ?>
  <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-7 xl:grid-cols-8 gap-3">
    <?php foreach ($items as $item): ?>
    <div class="group relative">
      <a href="watch.php?type=<?= htmlspecialchars($item['media_type'], ENT_QUOTES, 'UTF-8') ?>&id=<?= (int)$item['tmdb_id'] ?>" class="movie-card block">
        <img src="<?= htmlspecialchars(posterUrl($item['poster_path']), ENT_QUOTES, 'UTF-8') ?>"
             alt="<?= htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8') ?>" loading="lazy">
        <div class="overlay"></div>
        <div class="play-btn"><i class="fas fa-play text-sm"></i></div>
        <div class="badge-type"><?= $item['media_type'] === 'tv' ? 'TV' : 'Movie' ?></div>
      </a>
      <!-- Remove button -->
      <form method="post" action="actions.php" class="absolute top-1 left-1 opacity-0 group-hover:opacity-100 transition-opacity z-10">
        <input type="hidden" name="action" value="remove_watchlist">
        <input type="hidden" name="id" value="<?= (int)$item['id'] ?>">
        <button type="submit" title="Remove"
                class="w-6 h-6 rounded-full bg-red-500/90 hover:bg-red-500 flex items-center justify-center text-white transition-colors"
                onclick="return confirm('Remove from watchlist?')">
          <i class="fas fa-times text-xs"></i>
        </button>
      </form>
      <p class="text-xs text-white/70 mt-1 truncate"><?= htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8') ?></p>
    </div>
    <?php endforeach; ?>
  </div>

  <?php else: ?>
  <div class="text-center py-24">
    <div class="w-20 h-20 mx-auto rounded-full neu-raised flex items-center justify-center mb-6">
      <i class="fas fa-bookmark text-3xl text-white/20"></i>
    </div>
    <h2 class="text-xl font-bold text-white mb-2">Your watchlist is empty</h2>
    <p class="text-white/40 mb-6 text-sm">Save movies and TV shows you want to watch later.</p>
    <a href="search.php" class="btn-primary">
      <i class="fas fa-search"></i> Discover Content
    </a>
  </div>
  <?php endif; ?>
</main>

<?php layout_footer($pdo); ?>
