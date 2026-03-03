<?php
require __DIR__ . '/includes/db.php';
require __DIR__ . '/includes/helpers.php';
require __DIR__ . '/includes/layout.php';

$slug = trim($_GET['slug'] ?? '');
if (!$slug) { header('Location: index.php'); exit; }

$stmt = $pdo->prepare('SELECT * FROM pages WHERE slug = ? AND is_active = 1 LIMIT 1');
$stmt->execute([$slug]);
$page = $stmt->fetch();

if (!$page) {
    http_response_code(404);
    layout_head('Page Not Found', $pdo);
    layout_header($pdo);
    ?>
    <main class="max-w-2xl mx-auto px-4 py-20 text-center">
      <div class="w-16 h-16 mx-auto rounded-full neu-raised flex items-center justify-center mb-5">
        <i class="fas fa-file-slash text-3xl text-white/20"></i>
      </div>
      <h1 class="text-2xl font-black text-white mb-2">Page Not Found</h1>
      <p class="text-white/40 mb-6">The page you are looking for does not exist or has been removed.</p>
      <a href="index.php" class="btn-primary"><i class="fas fa-home"></i> Go Home</a>
    </main>
    <?php
    layout_footer($pdo);
    exit;
}

layout_head($page['title'], $pdo);
layout_header($pdo);
?>

<main class="max-w-3xl mx-auto px-4 py-10 pb-16">
  <!-- Breadcrumb -->
  <nav class="flex items-center gap-2 text-xs text-white/30 mb-6">
    <a href="index.php" class="hover:text-white/60 transition-colors"><i class="fas fa-home"></i></a>
    <i class="fas fa-chevron-right text-xs"></i>
    <span class="text-white/50"><?= htmlspecialchars($page['title'], ENT_QUOTES, 'UTF-8') ?></span>
  </nav>

  <!-- Page content -->
  <div class="neu-raised rounded-2xl p-6 md:p-8">
    <h1 class="text-2xl md:text-3xl font-black text-white mb-6 pb-4 border-b border-white/10">
      <?= htmlspecialchars($page['title'], ENT_QUOTES, 'UTF-8') ?>
    </h1>
    <div class="prose-custom text-sm text-white/70 leading-relaxed space-y-4">
      <?= nl2br(htmlspecialchars($page['content'], ENT_QUOTES, 'UTF-8')) ?>
    </div>
  </div>

  <div class="mt-6 text-center">
    <a href="index.php" class="btn-outline text-sm">
      <i class="fas fa-arrow-left"></i> Back to Home
    </a>
  </div>
</main>

<style>
.prose-custom p { margin-bottom: 0.75rem; }
.prose-custom h2 { font-size: 1.25rem; font-weight: 700; color: white; margin: 1.5rem 0 0.75rem; }
.prose-custom h3 { font-size: 1.1rem; font-weight: 600; color: rgba(255,255,255,0.87); margin: 1.25rem 0 0.5rem; }
.prose-custom a { color: #00e5ff; text-decoration: underline; }
.prose-custom a:hover { opacity: 0.8; }
.prose-custom ul { list-style: disc; padding-left: 1.5rem; }
.prose-custom ol { list-style: decimal; padding-left: 1.5rem; }
.prose-custom li { margin-bottom: 0.25rem; }
</style>

<?php layout_footer($pdo); ?>
