<?php
require __DIR__ . '/includes/db.php';
require __DIR__ . '/includes/helpers.php';
require __DIR__ . '/includes/layout.php';

$upiId   = setting($pdo, 'upi_id', '');
$upiQr   = setting($pdo, 'upi_qr_url', '');
$msg     = $_GET['msg'] ?? '';
$plan    = trim($_GET['plan'] ?? '');

// Plan price map
$planPrices = ['premium'=>99,'ultra'=>199];
$planPrice  = $planPrices[strtolower($plan)] ?? 99;

layout_head('UPI Payment', $pdo);
layout_header($pdo);
?>

<main class="max-w-lg mx-auto px-4 py-10 pb-16">

  <?php if ($msg === 'payment-submitted'): ?>
  <div class="alert alert-success mb-6">
    <i class="fas fa-check-circle text-lg mr-2"></i>
    <div>
      <p class="font-bold">Payment Submitted!</p>
      <p class="text-xs opacity-80 mt-0.5">Your payment is being verified. You'll get access soon.</p>
    </div>
  </div>
  <?php endif; ?>

  <!-- Header -->
  <div class="text-center mb-8">
    <div class="w-16 h-16 mx-auto rounded-2xl neu-raised flex items-center justify-center mb-4">
      <i class="fas fa-rupee-sign text-2xl text-primary"></i>
    </div>
    <h1 class="text-2xl font-black text-white">UPI Payment</h1>
    <p class="text-sm text-white/40 mt-1">Complete your subscription payment via UPI</p>
  </div>

  <!-- UPI QR + ID -->
  <?php if ($upiQr || $upiId): ?>
  <div class="neu-raised rounded-2xl p-6 text-center mb-6">
    <?php if ($upiQr): ?>
    <div class="mb-4">
      <img src="<?= htmlspecialchars($upiQr, ENT_QUOTES, 'UTF-8') ?>" alt="UPI QR Code"
           class="w-48 h-48 mx-auto rounded-xl object-contain" style="background:white;padding:8px">
    </div>
    <?php else: ?>
    <div class="w-48 h-48 mx-auto rounded-xl bg-white/5 flex items-center justify-center mb-4">
      <i class="fas fa-qrcode text-6xl text-white/20"></i>
    </div>
    <?php endif; ?>
    <?php if ($upiId): ?>
    <p class="text-xs text-white/40 mb-1">UPI ID</p>
    <div class="flex items-center justify-center gap-2">
      <span id="upiIdText" class="text-base font-bold text-primary font-mono"><?= htmlspecialchars($upiId, ENT_QUOTES, 'UTF-8') ?></span>
      <button onclick="copyUpi()" title="Copy UPI ID" class="p-1.5 rounded-lg hover:bg-white/5 text-white/40 hover:text-white transition-colors">
        <i class="fas fa-copy text-sm"></i>
      </button>
    </div>
    <?php endif; ?>
    <?php if ($plan): ?>
    <div class="mt-4 pt-4 border-t border-white/10">
      <span class="text-xs text-white/40">Plan: </span>
      <span class="text-sm font-bold text-white capitalize"><?= htmlspecialchars($plan, ENT_QUOTES, 'UTF-8') ?></span>
      <span class="mx-2 text-white/20">•</span>
      <span class="text-sm font-bold text-primary">₹<?= $planPrice ?>/mo</span>
    </div>
    <?php endif; ?>
    <p class="text-xs text-white/30 mt-3 leading-relaxed">
      Scan QR or pay to UPI ID above, then fill in the form below with your transaction details.
    </p>
  </div>
  <?php else: ?>
  <div class="neu-flat rounded-2xl p-6 text-center mb-6">
    <i class="fas fa-exclamation-triangle text-3xl text-yellow-400 mb-3"></i>
    <p class="text-white/60 text-sm">UPI payment not configured yet.</p>
    <?php if (isLoggedIn()): ?><a href="admin.php?tab=settings" class="btn-primary mt-3 text-sm inline-flex">Configure</a><?php endif; ?>
  </div>
  <?php endif; ?>

  <!-- Payment submission form -->
  <div class="neu-flat rounded-2xl p-6">
    <h2 class="text-base font-bold text-white mb-4 flex items-center gap-2">
      <i class="fas fa-file-invoice text-primary"></i> Submit Payment Details
    </h2>
    <form method="post" action="actions.php">
      <input type="hidden" name="action" value="submit_upi_payment">
      <div class="space-y-4">
        <div>
          <label class="text-xs font-semibold text-white/50 uppercase tracking-wider block mb-1.5">Your Name *</label>
          <div class="relative">
            <i class="fas fa-user absolute left-3 top-1/2 -translate-y-1/2 text-white/30 text-sm"></i>
            <input type="text" name="user_name" required placeholder="Enter your full name" class="input-dark pl-9 text-sm">
          </div>
        </div>
        <div>
          <label class="text-xs font-semibold text-white/50 uppercase tracking-wider block mb-1.5">Email *</label>
          <div class="relative">
            <i class="fas fa-envelope absolute left-3 top-1/2 -translate-y-1/2 text-white/30 text-sm"></i>
            <input type="email" name="user_email" required placeholder="your@email.com" class="input-dark pl-9 text-sm">
          </div>
        </div>
        <div>
          <label class="text-xs font-semibold text-white/50 uppercase tracking-wider block mb-1.5">Transaction ID / UTR *</label>
          <div class="relative">
            <i class="fas fa-hashtag absolute left-3 top-1/2 -translate-y-1/2 text-white/30 text-sm"></i>
            <input type="text" name="transaction_id" required placeholder="12-digit transaction ID" class="input-dark pl-9 text-sm font-mono">
          </div>
        </div>
        <div>
          <label class="text-xs font-semibold text-white/50 uppercase tracking-wider block mb-1.5">Amount Paid (₹) *</label>
          <div class="relative">
            <i class="fas fa-rupee-sign absolute left-3 top-1/2 -translate-y-1/2 text-white/30 text-sm"></i>
            <input type="number" step="1" min="1" name="amount" value="<?= $planPrice ?>" required placeholder="99" class="input-dark pl-9 text-sm">
          </div>
        </div>
        <button type="submit" class="btn-primary w-full justify-center py-3 text-sm font-bold rounded-xl">
          <i class="fas fa-paper-plane"></i> Submit Payment
        </button>
        <p class="text-xs text-white/30 text-center leading-relaxed">
          Your payment will be verified within 24 hours. For help, contact support.
        </p>
      </div>
    </form>
  </div>

  <div class="mt-4 text-center">
    <a href="pricing.php" class="text-xs text-white/30 hover:text-white/60 transition-colors">
      <i class="fas fa-arrow-left mr-1"></i>Back to Pricing
    </a>
  </div>
</main>

<script>
function copyUpi(){
  var text = document.getElementById('upiIdText');
  if(text && navigator.clipboard){
    navigator.clipboard.writeText(text.textContent.trim()).then(function(){
      alert('UPI ID copied!');
    });
  } else if(text) {
    var ta = document.createElement('textarea');
    ta.value = text.textContent.trim();
    document.body.appendChild(ta);
    ta.select();
    document.execCommand('copy');
    document.body.removeChild(ta);
    alert('UPI ID copied!');
  }
}
</script>

<?php layout_footer($pdo); ?>
