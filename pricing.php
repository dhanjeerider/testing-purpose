<?php
require __DIR__ . '/includes/db.php';
require __DIR__ . '/includes/helpers.php';
require __DIR__ . '/includes/layout.php';

$msg        = $_GET['msg'] ?? '';
$payEnabled = setting($pdo, 'subscription_enabled', '0') === '1';
$upiId      = setting($pdo, 'upi_id', '');

layout_head('Pricing', $pdo);
layout_header($pdo);
?>

<main class="max-w-4xl mx-auto px-4 py-10 pb-16">

  <!-- Header -->
  <div class="text-center mb-10">
    <span class="inline-block px-3 py-1 rounded-full text-xs font-bold bg-primary/15 text-primary mb-3">
      <i class="fas fa-crown mr-1"></i>Premium Access
    </span>
    <h1 class="text-3xl md:text-4xl font-black text-white mb-3">Simple, Transparent Pricing</h1>
    <p class="text-white/50 max-w-md mx-auto text-sm">Get unlimited access to all movies and TV shows with a single subscription.</p>
  </div>

  <!-- Billing toggle -->
  <div class="flex items-center justify-center gap-3 mb-8" id="billingToggle">
    <span id="monthlyLabel" class="text-sm font-semibold text-primary">Monthly</span>
    <button onclick="toggleBilling()" id="toggleBtn"
            class="relative w-12 h-6 rounded-full transition-all duration-300 cursor-pointer"
            style="background:rgba(0,229,255,0.2);border:2px solid rgba(0,229,255,0.4)">
      <span id="toggleThumb" class="absolute top-0.5 left-0.5 w-4 h-4 rounded-full bg-primary transition-transform duration-300"></span>
    </button>
    <span id="yearlyLabel" class="text-sm font-medium text-white/40">Yearly <span class="text-green-400 text-xs font-bold">Save 30%</span></span>
  </div>

  <!-- Plan cards -->
  <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-10">
    <?php
    $plans = [
      [
        'name'      => 'Free',
        'icon'      => 'fas fa-tv',
        'monthly'   => 0,
        'yearly'    => 0,
        'color'     => 'rgba(255,255,255,0.1)',
        'border'    => 'rgba(255,255,255,0.15)',
        'popular'   => false,
        'features'  => ['Browse movies & TV shows','Standard quality','With ads','Community features','Basic watchlist'],
        'disabled'  => ['Download content','4K streaming','Ad-free experience','Priority support'],
      ],
      [
        'name'      => 'Premium',
        'icon'      => 'fas fa-crown',
        'monthly'   => 99,
        'yearly'    => 829,
        'color'     => 'rgba(0,229,255,0.1)',
        'border'    => '#00e5ff',
        'popular'   => true,
        'features'  => ['Everything in Free','HD & Full HD streaming','Ad-free experience','Download content','Priority support','Unlimited watchlist'],
        'disabled'  => ['4K Ultra HD streaming'],
      ],
      [
        'name'      => 'Ultra',
        'icon'      => 'fas fa-gem',
        'monthly'   => 199,
        'yearly'    => 1499,
        'color'     => 'rgba(255,62,141,0.08)',
        'border'    => '#ff3e8d',
        'popular'   => false,
        'features'  => ['Everything in Premium','4K Ultra HD streaming','Multiple device streaming','Exclusive early access','Dedicated support','All future features'],
        'disabled'  => [],
      ],
    ];
    foreach ($plans as $plan):
    ?>
    <div class="relative neu-flat rounded-2xl p-6 flex flex-col transition-transform hover:-translate-y-1"
         style="background:<?= $plan['color'] ?>;border:2px solid <?= $plan['border'] ?>">
      <?php if ($plan['popular']): ?>
      <div class="absolute -top-3 left-1/2 -translate-x-1/2">
        <span class="bg-primary text-black text-xs font-black px-3 py-1 rounded-full">MOST POPULAR</span>
      </div>
      <?php endif; ?>
      <!-- Plan header -->
      <div class="text-center mb-5">
        <div class="w-12 h-12 mx-auto rounded-xl flex items-center justify-center mb-3" style="background:<?= $plan['border'] ?>22">
          <i class="<?= $plan['icon'] ?> text-xl" style="color:<?= $plan['border'] ?>"></i>
        </div>
        <h3 class="text-lg font-black text-white"><?= $plan['name'] ?></h3>
        <div class="mt-2">
          <span class="price-monthly <?= $plan['monthly'] === 0 ? '' : '' ?>">
            <?php if ($plan['monthly'] === 0): ?>
            <span class="text-3xl font-black text-white">Free</span>
            <?php else: ?>
            <span class="text-3xl font-black text-white">₹<?= $plan['monthly'] ?></span>
            <span class="text-white/40 text-sm">/mo</span>
            <?php endif; ?>
          </span>
          <span class="price-yearly hidden">
            <?php if ($plan['yearly'] === 0): ?>
            <span class="text-3xl font-black text-white">Free</span>
            <?php else: ?>
            <span class="text-3xl font-black text-white">₹<?= $plan['yearly'] ?></span>
            <span class="text-white/40 text-sm">/yr</span>
            <?php endif; ?>
          </span>
        </div>
      </div>
      <!-- Features -->
      <ul class="space-y-2 flex-1 mb-5">
        <?php foreach ($plan['features'] as $f): ?>
        <li class="flex items-center gap-2 text-sm text-white/70">
          <i class="fas fa-check-circle text-xs" style="color:<?= $plan['border'] ?>"></i><?= htmlspecialchars($f, ENT_QUOTES, 'UTF-8') ?>
        </li>
        <?php endforeach; ?>
        <?php foreach ($plan['disabled'] as $f): ?>
        <li class="flex items-center gap-2 text-sm text-white/25 line-through">
          <i class="fas fa-times-circle text-xs text-white/15"></i><?= htmlspecialchars($f, ENT_QUOTES, 'UTF-8') ?>
        </li>
        <?php endforeach; ?>
      </ul>
      <!-- CTA button -->
      <?php if ($plan['monthly'] === 0): ?>
      <a href="index.php" class="btn-outline w-full justify-center py-3 text-sm font-bold rounded-xl">
        Get Started Free
      </a>
      <?php elseif ($payEnabled || $upiId): ?>
      <a href="upi-payment.php?plan=<?= urlencode(strtolower($plan['name'])) ?>" class="w-full justify-center py-3 text-sm font-bold rounded-xl inline-flex items-center gap-2" style="background:<?= $plan['border'] ?>;color:<?= $plan['popular'] ? '#000' : '#fff' ?>">
        <i class="fas fa-bolt"></i> Subscribe Now
      </a>
      <?php else: ?>
      <button disabled class="w-full py-3 rounded-xl text-sm font-bold text-white/30 cursor-not-allowed" style="background:rgba(255,255,255,0.05)">
        Coming Soon
      </button>
      <?php endif; ?>
    </div>
    <?php endforeach; ?>
  </div>

  <!-- Payment methods -->
  <div class="neu-flat rounded-2xl p-5 mb-6">
    <h3 class="text-sm font-bold text-white/60 text-center uppercase tracking-wider mb-4">Accepted Payment Methods</h3>
    <div class="flex flex-wrap items-center justify-center gap-4">
      <?php
      $methods = [
        ['UPI','fas fa-mobile-alt','#00e5ff'],
        ['Razorpay','fas fa-credit-card','#528FF0'],
        ['Debit Card','far fa-credit-card','#60d394'],
        ['Net Banking','fas fa-university','#ff9b4b'],
        ['Wallet','fas fa-wallet','#c77dff'],
      ];
      foreach ($methods as [$name,$icon,$color]):
      ?>
      <div class="flex items-center gap-2 px-4 py-2 rounded-xl" style="background:<?= $color ?>18;border:1px solid <?= $color ?>35">
        <i class="<?= $icon ?> text-sm" style="color:<?= $color ?>"></i>
        <span class="text-xs text-white/70"><?= $name ?></span>
      </div>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- FAQ -->
  <div class="neu-flat rounded-2xl p-5">
    <h3 class="text-base font-bold text-white mb-4 flex items-center gap-2"><i class="fas fa-question-circle text-primary"></i> FAQ</h3>
    <div class="space-y-3">
      <?php
      $faqs = [
        ['Can I cancel anytime?','Yes! You can cancel your subscription at any time. No questions asked.'],
        ['What devices are supported?','All devices including smartphones, tablets, laptops, and smart TVs.'],
        ['Is there a free trial?','Browse our library for free. Premium features require a subscription.'],
        ['How is payment processed?','We support UPI, credit/debit cards, and net banking via secure payment gateways.'],
      ];
      foreach ($faqs as $i => [$q,$a]):
      ?>
      <details class="group">
        <summary class="flex items-center justify-between cursor-pointer py-2.5 text-sm font-medium text-white/80 hover:text-white transition-colors list-none">
          <span><?= htmlspecialchars($q, ENT_QUOTES, 'UTF-8') ?></span>
          <i class="fas fa-chevron-down text-white/30 group-open:rotate-180 transition-transform text-xs"></i>
        </summary>
        <p class="text-sm text-white/40 pb-3 leading-relaxed"><?= htmlspecialchars($a, ENT_QUOTES, 'UTF-8') ?></p>
        <?php if ($i < count($faqs) - 1): ?><hr class="border-white/5"><?php endif; ?>
      </details>
      <?php endforeach; ?>
    </div>
  </div>

</main>

<script>
var isYearly = false;
function toggleBilling(){
  isYearly = !isYearly;
  var thumb = document.getElementById('toggleThumb');
  var ml    = document.getElementById('monthlyLabel');
  var yl    = document.getElementById('yearlyLabel');
  thumb.style.transform = isYearly ? 'translateX(24px)' : 'translateX(0)';
  ml.classList.toggle('text-primary', !isYearly);
  ml.classList.toggle('text-white/40', isYearly);
  yl.classList.toggle('text-primary', isYearly);
  yl.classList.toggle('text-white/40', !isYearly);
  document.querySelectorAll('.price-monthly').forEach(function(el){ el.classList.toggle('hidden', isYearly); });
  document.querySelectorAll('.price-yearly').forEach(function(el){ el.classList.toggle('hidden', !isYearly); });
}
</script>

<?php layout_footer($pdo); ?>
