<?php
require __DIR__ . '/includes/db.php';
require __DIR__ . '/includes/helpers.php';
require __DIR__ . '/includes/layout.php';

// Redirect if already logged in
if (isLoggedIn()) {
    redirect('admin.php');
}

$error  = $_GET['error'] ?? '';
$reset  = $_GET['reset'] ?? '';
$config = require __DIR__ . '/config.php';

layout_head('Admin Login', $pdo);
layout_header($pdo);
?>

<main class="max-w-md mx-auto px-4 py-12">
  <div class="text-center mb-8">
    <div class="w-16 h-16 mx-auto rounded-2xl neu-raised flex items-center justify-center mb-4">
      <i class="fas fa-shield-alt text-2xl text-primary"></i>
    </div>
    <h1 class="text-2xl font-black text-white">Admin Login</h1>
    <p class="text-sm text-white/40 mt-1">Sign in to manage your site</p>
  </div>

  <?php if ($reset): ?>
  <div class="alert alert-success mb-4">
    <i class="fas fa-key mr-2"></i>
    Password reset! Your new password is: <strong class="font-mono"><?= htmlspecialchars($reset, ENT_QUOTES, 'UTF-8') ?></strong>
    <br><small class="text-xs opacity-75">Please change it after logging in.</small>
  </div>
  <?php endif; ?>

  <?php if ($error === 'invalid-login'): ?>
  <div class="alert alert-error mb-4">
    <i class="fas fa-exclamation-circle mr-2"></i> Invalid username or password.
  </div>
  <?php elseif ($error === 'email-not-match'): ?>
  <div class="alert alert-error mb-4">
    <i class="fas fa-exclamation-circle mr-2"></i> Email address does not match admin account.
  </div>
  <?php endif; ?>

  <!-- Login Form -->
  <div class="neu-raised rounded-2xl p-6 mb-4">
    <form method="post" action="actions.php">
      <input type="hidden" name="action" value="login">
      <div class="space-y-4">
        <div>
          <label class="block text-xs font-semibold text-white/60 mb-1.5 uppercase tracking-wider">Username</label>
          <div class="relative">
            <i class="fas fa-user absolute left-3 top-1/2 -translate-y-1/2 text-white/30 text-sm"></i>
            <input type="text" name="username" required placeholder="admin"
                   class="input-dark pl-9" autocomplete="username">
          </div>
        </div>
        <div>
          <label class="block text-xs font-semibold text-white/60 mb-1.5 uppercase tracking-wider">Password</label>
          <div class="relative">
            <i class="fas fa-lock absolute left-3 top-1/2 -translate-y-1/2 text-white/30 text-sm"></i>
            <input type="password" name="password" id="passwordInput" required placeholder="••••••••"
                   class="input-dark pl-9 pr-10" autocomplete="current-password">
            <button type="button" onclick="togglePw()" class="absolute right-3 top-1/2 -translate-y-1/2 text-white/30 hover:text-white/60 transition-colors">
              <i class="fas fa-eye" id="pwEyeIcon"></i>
            </button>
          </div>
        </div>
        <button type="submit" class="btn-primary w-full justify-center py-3 text-sm font-bold rounded-xl">
          <i class="fas fa-sign-in-alt"></i> Sign In
        </button>
      </div>
    </form>
  </div>

  <!-- Forgot Password -->
  <details class="neu-flat rounded-2xl overflow-hidden">
    <summary class="px-6 py-4 cursor-pointer text-sm text-white/50 hover:text-white/80 transition-colors list-none flex items-center gap-2">
      <i class="fas fa-question-circle text-primary"></i> Forgot password?
    </summary>
    <div class="px-6 pb-5 pt-2">
      <p class="text-xs text-white/40 mb-3">Enter your admin email to reset password.</p>
      <form method="post" action="actions.php">
        <input type="hidden" name="action" value="forgot_password">
        <div class="flex gap-2">
          <input type="email" name="email" placeholder="admin@example.com" required class="input-dark text-sm flex-1">
          <button type="submit" class="btn-primary text-sm py-2 px-4 flex-shrink-0">Reset</button>
        </div>
      </form>
    </div>
  </details>

  <p class="text-center text-xs text-white/25 mt-6">
    <a href="index.php" class="hover:text-white/50 transition-colors"><i class="fas fa-arrow-left mr-1"></i>Back to site</a>
  </p>
</main>

<script>
function togglePw(){
  var input = document.getElementById('passwordInput');
  var icon  = document.getElementById('pwEyeIcon');
  if(input.type === 'password'){
    input.type = 'text';
    icon.className = 'fas fa-eye-slash';
  } else {
    input.type = 'password';
    icon.className = 'fas fa-eye';
  }
}
</script>

<?php layout_footer($pdo); ?>
