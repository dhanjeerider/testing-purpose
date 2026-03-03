<?php
require __DIR__ . '/../includes/db.php';

$schemaPath = __DIR__ . '/../database/schema.sql';
$sql = file_get_contents($schemaPath);
$pdo->exec($sql);

$defaults = [
    'site_title' => 'TMovie PHP',
    'site_description' => 'Watch trending movies and TV shows',
    'telegram_link' => 'https://t.me/',
    'subscription_enabled' => '0',
    'google_analytics_id' => '',
    'search_console_meta' => '',
    'admin_email' => 'admin@example.com',
    'upi_id' => '',
    'upi_qr_url' => '',
    'razorpay_key_id' => '',
    'razorpay_key_secret' => '',
    'video_ad_enabled' => '0',
    'video_ad_type' => 'image',
    'video_ad_url' => '',
    'video_ad_skip_seconds' => '5'
];

$stmt = $pdo->prepare('INSERT OR IGNORE INTO site_settings (setting_key, setting_value) VALUES (:k, :v)');
foreach ($defaults as $key => $value) {
    $stmt->execute([':k' => $key, ':v' => $value]);
}

$adminExists = $pdo->query("SELECT COUNT(*) AS c FROM users WHERE username = 'admin'")->fetch()['c'];
if ((int)$adminExists === 0) {
    $ins = $pdo->prepare('INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)');
    $ins->execute(['admin', 'admin@example.com', password_hash('admin123', PASSWORD_BCRYPT), 'admin']);
}

$serverCount = $pdo->query('SELECT COUNT(*) AS c FROM custom_servers')->fetch()['c'];
if ((int)$serverCount === 0) {
    $servers = [
        ['VidSrcVIP', 'stream', 'https://vidsrc.vip/embed/movie/{id}', 'https://vidsrc.vip/embed/tv/{id}/{season}/{episode}', 0, 1, 0, 1],
        ['VidLink', 'stream', 'https://vidlink.pro/movie/{id}', 'https://vidlink.pro/tv/{id}/{season}/{episode}', 1, 0, 0, 1],
        ['BunnyDDL', 'download', 'https://example.com/download/{id}/720p', 'https://example.com/download-tv/{id}/{season}/{episode}/720p', 0, 0, 1, 1]
    ];
    $ins = $pdo->prepare('INSERT INTO custom_servers (name, type, url, url_tv, has_ads, has_4k, is_download, is_default, is_active, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1, 0)');
    foreach ($servers as $s) {
        $ins->execute($s);
    }
}

echo "Database initialized successfully.\n";
